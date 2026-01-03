<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Cost;

class CostSyncService
{
    /**
     * Map task types to cost categories
     */
    private static array $taskTypeToCategoryMap = [
        'harvesting' => 'labor',
        'spraying' => 'labor',
        'grass_cut' => 'labor',
        'sanitation' => 'labor',
        'maintenance' => 'maintenance',
        'manuring' => 'labor',
        'other' => 'other',
    ];

    /**
     * Create a cost record from a task
     * Called when a task is created or updated
     */
    public static function syncTaskCost(Task $task): void
    {
        // Only sync costs if task has a cost value
        if (!$task->cost || $task->cost <= 0) {
            return;
        }

        // Ensure workers are loaded
        $task->load('workers');

        // Get the category based on task type
        $category = self::$taskTypeToCategoryMap[$task->type] ?? 'other';

        // Generate description
        $description = self::generateCostDescription($task);

        // Get supplier (worker names)
        $supplier = self::getSupplierName($task);

        // Generate notes with payment details
        $notes = self::generatePaymentNotes($task);

        // Check if a cost record already exists for this task
        $existingCost = Cost::where('task_id', $task->id)->first();

        if ($existingCost) {
            // Update existing cost record
            $existingCost->update([
                'date' => $task->work_date,
                'category' => $category,
                'description' => $description,
                'amount' => $task->cost,
                'supplier' => $supplier,
                'notes' => $notes,
            ]);
        } else {
            // Create new cost record
            Cost::create([
                'task_id' => $task->id,
                'date' => $task->work_date,
                'category' => $category,
                'description' => $description,
                'amount' => $task->cost,
                'supplier' => $supplier,
                'notes' => $notes,
            ]);
        }
    }

    /**
     * Remove cost record when task is deleted
     */
    public static function removeCost(Task $task): void
    {
        Cost::where('task_id', $task->id)->delete();
    }

    /**
     * Generate a descriptive cost description from task
     */
    private static function generateCostDescription(Task $task): string
    {
        $typeLabel = Task::TYPES[$task->type] ?? $task->type;
        $title = $task->title ?: "Task {$task->id}";

        return "{$typeLabel}: {$title}";
    }

    /**
     * Get supplier name from task workers
     */
    private static function getSupplierName(Task $task): ?string
    {
        $workers = $task->workers()->pluck('name')->toArray();
        
        if (empty($workers)) {
            return null;
        }

        return implode(', ', $workers);
    }

    /**
     * Generate payment notes with rate and quantity details
     */
    private static function generatePaymentNotes(Task $task): ?string
    {
        $notes = [];

        // Add rate information
        if ($task->rate && $task->rate > 0) {
            if ($task->type === 'harvesting') {
                $notes[] = "Rate: RM" . number_format($task->rate, 2) . " per kg";
            } elseif ($task->type === 'manuring') {
                $notes[] = "Rate: RM" . number_format($task->rate, 2) . " per tree";
            } else {
                $notes[] = "Rate: RM" . number_format($task->rate, 2) . " per job per worker";
            }
        }

        // Add quantity information for harvesting
        if ($task->type === 'harvesting' && $task->quantity_kg && $task->quantity_kg > 0) {
            $notes[] = "Harvest Amount: " . number_format($task->quantity_kg, 2) . " kg";
        }

        // Add worker count for non-harvesting tasks
        if ($task->type !== 'harvesting') {
            $workerCount = $task->workers()->count();
            if ($workerCount > 0) {
                $notes[] = "Workers: " . $workerCount;
            }
        }

        // Add tree count for manuring
        if ($task->type === 'manuring' && $task->tree_count && $task->tree_count > 0) {
            $notes[] = "Trees: " . (int) $task->tree_count;
        }

        return !empty($notes) ? implode(' | ', $notes) : null;
    }
}
