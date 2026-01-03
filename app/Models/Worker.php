<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'contact',
        'status',
        'notes',
    ];

    public const ROLES = [
        'general_worker' => 'General Worker',
        'foreman' => 'Foreman',
        'supervisor' => 'Supervisor',
        'manager' => 'Manager',
    ];

    public const STATUSES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];

    /**
     * Get all tasks assigned to this worker
     */
    public function tasks()
    {
        return $this->belongsToMany(Task::class)->withTimestamps()->orderByDesc('work_date');
    }

    /**
     * Get total earnings for this worker
     */
    public function getTotalEarnings()
    {
        return $this->tasks()->whereNotNull('cost')->sum('cost');
    }

    /**
     * Get earnings for a specific month
     */
    public function getMonthlyEarnings($year, $month)
    {
        return $this->tasks()
            ->whereYear('work_date', $year)
            ->whereMonth('work_date', $month)
            ->whereNotNull('cost')
            ->get();
    }
}
