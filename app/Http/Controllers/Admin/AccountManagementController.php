<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AccountManagementController extends Controller
{
    /**
     * Show all pending and active accounts.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['name', 'email', 'created_at', 'status'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.accounts.index', compact('users'));
    }

    /**
     * Show account details.
     */
    public function show(User $user)
    {
        // Get user's activity logs
        $activityLogs = $user->activityLogs()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.accounts.show', compact('user', 'activityLogs'));
    }

    /**
     * Approve a pending account.
     */
    public function approve(User $user)
    {
        if ($user->status !== 'pending') {
            return back()->with('error', 'Only pending accounts can be approved.');
        }

        $user->update([
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', "Account for {$user->name} has been approved!");
    }

    /**
     * Reject a pending account.
     */
    public function reject(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if ($user->status !== 'pending') {
            return back()->with('error', 'Only pending accounts can be rejected.');
        }

        $user->update([
            'status' => 'inactive',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => $request->reason,
        ]);

        return back()->with('success', "Account for {$user->name} has been rejected.");
    }

    /**
     * Deactivate an active account.
     */
    public function deactivate(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['status' => 'inactive']);

        return back()->with('success', "Account for {$user->name} has been deactivated.");
    }

    /**
     * Reactivate an inactive account.
     */
    public function reactivate(User $user)
    {
        $user->update(['status' => 'active']);

        return back()->with('success', "Account for {$user->name} has been reactivated.");
    }

    /**
     * Promote a user to admin.
     */
    public function promoteToAdmin(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'This user is already an admin.');
        }

        $user->update(['role' => 'admin']);

        return back()->with('success', "{$user->name} has been promoted to admin.");
    }

    /**
     * Demote an admin to user.
     */
    public function demoteToUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot demote yourself.');
        }

        if (!$user->isAdmin()) {
            return back()->with('error', 'This user is not an admin.');
        }

        $user->update(['role' => 'user']);

        return back()->with('success', "{$user->name} has been demoted to user.");
    }
}
