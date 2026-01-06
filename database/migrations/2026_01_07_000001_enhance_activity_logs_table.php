<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            // Add URL and HTTP method for tracking exact requests
            $table->string('url', 500)->nullable()->after('event_type');
            $table->string('http_method', 10)->nullable()->after('url');
            
            // Add referer to track where users came from
            $table->string('referer', 500)->nullable()->after('user_agent');
            
            // Add module/resource for better categorization
            $table->string('module', 100)->nullable()->after('referer');
            $table->string('resource_type', 100)->nullable()->after('module');
            $table->unsignedBigInteger('resource_id')->nullable()->after('resource_type');
            
            // Add duration for performance tracking (in milliseconds)
            $table->integer('duration_ms')->nullable()->after('properties');
            
            // Add session ID for tracking user sessions
            $table->string('session_id', 100)->nullable()->after('duration_ms');
            
            // Add indexes for better query performance
            $table->index('url');
            $table->index('http_method');
            $table->index('module');
            $table->index(['resource_type', 'resource_id']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['url']);
            $table->dropIndex(['http_method']);
            $table->dropIndex(['module']);
            $table->dropIndex(['resource_type', 'resource_id']);
            $table->dropIndex(['session_id']);
            
            $table->dropColumn([
                'url',
                'http_method',
                'referer',
                'module',
                'resource_type',
                'resource_id',
                'duration_ms',
                'session_id',
            ]);
        });
    }
};
