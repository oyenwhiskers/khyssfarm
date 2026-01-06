<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    /**
     * Routes to exclude from tracking
     */
    protected array $excludedRoutes = [
        'logout',
        'account.pending',
    ];

    /**
     * URL patterns to exclude from tracking
     */
    protected array $excludedPatterns = [
        'livewire/*',
        '_debugbar/*',
        'api/trends-data',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        // Only track for authenticated users
        if (!Auth::check()) {
            return $response;
        }

        // Skip if route should be excluded
        if ($this->shouldExclude($request)) {
            return $response;
        }

        // Calculate duration
        $duration = (microtime(true) - $startTime) * 1000; // Convert to milliseconds

        // Log the activity
        $this->logActivity($request, $response, $duration);

        return $response;
    }

    /**
     * Check if the request should be excluded from tracking
     */
    protected function shouldExclude(Request $request): bool
    {
        // Exclude based on route name
        if ($request->route() && in_array($request->route()->getName(), $this->excludedRoutes)) {
            return true;
        }

        // Exclude based on URL patterns
        foreach ($this->excludedPatterns as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        // Exclude AJAX polling requests
        if ($request->ajax() && $request->isMethod('GET')) {
            return true;
        }

        return false;
    }

    /**
     * Log the user activity
     */
    protected function logActivity(Request $request, Response $response, float $duration): void
    {
        $eventType = $this->determineEventType($request, $response);
        $module = $this->extractModule($request);
        $resourceInfo = $this->extractResourceInfo($request);
        
        ActivityLog::create([
            'user_id' => Auth::id(),
            'event_type' => $eventType,
            'url' => $request->fullUrl(),
            'http_method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'module' => $module,
            'resource_type' => $resourceInfo['type'],
            'resource_id' => $resourceInfo['id'],
            'description' => $this->generateDescription($request, $module, $resourceInfo),
            'properties' => $this->extractProperties($request, $response),
            'duration_ms' => round($duration, 2),
            'session_id' => session()->getId(),
        ]);
    }

    /**
     * Determine the event type based on the request
     */
    protected function determineEventType(Request $request, Response $response): string
    {
        $method = $request->method();
        $statusCode = $response->getStatusCode();

        return match($method) {
            'GET' => $statusCode === 200 ? 'page_view' : 'page_error',
            'POST' => $this->isStore($request) ? 'create' : 'action',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'request',
        };
    }

    /**
     * Check if the request is a store operation
     */
    protected function isStore(Request $request): bool
    {
        $route = $request->route();
        if (!$route) {
            return false;
        }

        $action = $route->getActionMethod();
        return in_array($action, ['store', 'create']);
    }

    /**
     * Extract the module name from the request
     */
    protected function extractModule(Request $request): ?string
    {
        $route = $request->route();
        if (!$route) {
            return null;
        }

        $name = $route->getName();
        if (!$name) {
            return null;
        }

        // Extract module from route name (e.g., 'harvests.index' -> 'harvests')
        $parts = explode('.', $name);
        return $parts[0] ?? null;
    }

    /**
     * Extract resource type and ID from the request
     */
    protected function extractResourceInfo(Request $request): array
    {
        $route = $request->route();
        if (!$route) {
            return ['type' => null, 'id' => null];
        }

        $parameters = $route->parameters();
        
        // Try to find the resource from route parameters
        foreach ($parameters as $key => $value) {
            // Skip pagination and other non-resource parameters
            if (in_array($key, ['page', 'sort', 'filter'])) {
                continue;
            }

            // If it's a model instance
            if (is_object($value) && method_exists($value, 'getKey')) {
                return [
                    'type' => class_basename($value),
                    'id' => $value->getKey(),
                ];
            }

            // If it's an ID
            if (is_numeric($value)) {
                return [
                    'type' => ucfirst($key),
                    'id' => $value,
                ];
            }
        }

        return ['type' => null, 'id' => null];
    }

    /**
     * Generate a human-readable description
     */
    protected function generateDescription(Request $request, ?string $module, array $resourceInfo): string
    {
        $user = Auth::user();
        $method = $request->method();
        
        $action = match($method) {
            'GET' => 'viewed',
            'POST' => 'created',
            'PUT', 'PATCH' => 'updated',
            'DELETE' => 'deleted',
            default => 'accessed',
        };

        if ($module && $resourceInfo['id']) {
            return "{$user->name} {$action} {$module} #{$resourceInfo['id']}";
        }

        if ($module) {
            return "{$user->name} {$action} {$module}";
        }

        return "{$user->name} {$action} " . $request->path();
    }

    /**
     * Extract additional properties from the request
     */
    protected function extractProperties(Request $request, Response $response): array
    {
        return [
            'status_code' => $response->getStatusCode(),
            'route_name' => $request->route()?->getName(),
            'route_action' => $request->route()?->getActionMethod(),
            'query_params' => $request->query->count() > 0 ? $request->query->all() : null,
            'has_file_upload' => $request->hasFile('*'),
        ];
    }
}
