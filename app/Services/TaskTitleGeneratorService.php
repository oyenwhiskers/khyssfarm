<?php

namespace App\Services;

use App\Models\Task;
use Carbon\Carbon;

class TaskTitleGeneratorService
{
    /**
     * Type prefixes for auto-generated titles
     */
    private static array $typePrefixes = [
        'harvesting' => 'HA',
        'spraying' => 'SP',
        'grass_cut' => 'GC',
        'sanitation' => 'SA',
        'maintenance' => 'MA',
        'manuring' => 'MN',
        'other' => 'OT',
    ];

    /**
     * Generate an auto task title based on type, date, and job number
     * Format: [PREFIX][YYYYMMDD]/[JOB_NUMBER]
     * Example: HA20260101/10
     * 
     * @param string $type Task type (harvesting, spraying, etc.)
     * @param Carbon $workDate The work date
     * @param bool $getJobNumber If true, calculate the job number for that month
     * @return string Generated title
     */
    public static function generateTitle(string $type, Carbon $workDate, bool $getJobNumber = true): string
    {
        // Get type prefix
        $prefix = self::$typePrefixes[$type] ?? 'OT';

        // Format date as YYYYMMDD
        $dateFormatted = $workDate->format('Ymd');

        // Get job number for this type in the work month
        $jobNumber = $getJobNumber ? self::getNextJobNumber($type, $workDate) : 1;

        return "{$prefix}{$dateFormatted}/{$jobNumber}";
    }

    /**
     * Get the job number (count of jobs of this type for this month)
     * 
     * @param string $type Task type
     * @param Carbon $workDate The work date
     * @return int Job number
     */
    public static function getNextJobNumber(string $type, Carbon $workDate): int
    {
        $monthStart = $workDate->copy()->startOfMonth();
        $monthEnd = $workDate->copy()->endOfMonth();

        // Count tasks of this type created in this month
        $count = Task::where('type', $type)
            ->whereBetween('work_date', [$monthStart, $monthEnd])
            ->count();

        // Return count + 1 (next job number)
        return $count + 1;
    }

    /**
     * Check if a task should have an auto-generated title
     * Returns true for preset tasks (non-custom tasks)
     * 
     * @param string $type
     * @return bool
     */
    public static function shouldAutoGenerateTitle(string $type): bool
    {
        // All defined types should have auto-generated titles
        return isset(self::$typePrefixes[$type]);
    }
}
