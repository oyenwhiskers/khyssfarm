<?php

namespace App\Services;

use App\Models\Harvest;
use App\Models\Task;
use Carbon\Carbon;

class HarvestSyncService
{
    /**
     * Get the week start and end dates (Monday to Sunday) for a given date
     * 
     * @param Carbon $date
     * @return array ['week_start' => Carbon, 'week_end' => Carbon]
     */
    public static function getWeekBoundaries(Carbon $date): array
    {
        // Get Monday of the week containing this date
        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        return [
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
        ];
    }

    /**
     * Find or create a Harvest batch for the week containing the given date
     * 
     * @param Carbon $date
     * @return Harvest
     */
    public static function findOrCreateBatchForDate(Carbon $date): Harvest
    {
        $boundaries = self::getWeekBoundaries($date);
        
        // Find existing batch for this week
        $batch = Harvest::whereBetween('harvest_date', [
            $boundaries['week_start'],
            $boundaries['week_end']
        ])->first();

        // If no batch exists, create one on the week start date
        if (!$batch) {
            $batch = Harvest::create([
                'harvest_date' => $boundaries['week_start']->toDateString(),
                'quantity_kg' => 0,
                'notes' => "Auto-created batch for week of {$boundaries['week_start']->format('M d, Y')}",
            ]);
        }

        return $batch;
    }

    /**
     * Sync a harvesting task with the Harvest batch
     * Called when task status changes to 'completed'
     * 
     * @param Task $task
     * @return void
     */
    public static function syncTaskToHarvest(Task $task): void
    {
        // Only process harvesting tasks
        if ($task->type !== 'harvesting') {
            return;
        }

        // Only sync if task is completed
        if ($task->status !== 'completed') {
            return;
        }

        // Find or create batch for this week
        $batch = self::findOrCreateBatchForDate($task->work_date);

        // If task is already synced to this batch, don't sync again
        if ($task->harvest_batch_id === $batch->id) {
            return;
        }

        // If task was previously synced to a different batch, unsync it first
        if ($task->harvest_batch_id) {
            self::removeBatchQuantity($task);
        }

        // Add quantity to the batch
        $batch->increment('quantity_kg', $task->quantity_kg);

        // Update task with batch reference without re-triggering observers
        $task->forceFill(['harvest_batch_id' => $batch->id])->saveQuietly();
    }

    /**
     * Remove task quantity from its associated Harvest batch
     * Called when task status changes from 'completed' to other status or when task is deleted
     * 
     * @param Task $task
     * @param bool $isDeleting Whether the task is being deleted (default: false)
     * @return void
     */
    public static function removeBatchQuantity(Task $task, bool $isDeleting = false): void
    {
        // If task is not synced to a batch, nothing to remove
        if (!$task->harvest_batch_id) {
            return;
        }

        $batch = Harvest::find($task->harvest_batch_id);

        if ($batch) {
            // Decrement quantity (ensure it doesn't go below 0)
            $newQuantity = max(0, (float) $batch->quantity_kg - (float) $task->quantity_kg);
            $batch->update(['quantity_kg' => $newQuantity]);

            // Delete batch if it's empty and was auto-created
            if ($newQuantity === 0 && str_contains($batch->notes ?? '', 'Auto-created batch')) {
                $batch->delete();
            }
        }

        // Clear batch reference from task (only if not deleting)
        if (!$isDeleting) {
            $task->forceFill(['harvest_batch_id' => null])->saveQuietly();
        }
    }

    /**
     * Handle quantity change in a completed task
     * Called when a completed task's quantity_kg is modified
     * 
     * @param Task $task
     * @param float $oldQuantity
     * @return void
     */
    public static function syncQuantityChange(Task $task, float $oldQuantity): void
    {
        // Only process if task is completed and has a batch
        if ($task->status !== 'completed' || !$task->harvest_batch_id) {
            return;
        }

        $batch = Harvest::find($task->harvest_batch_id);

        if ($batch) {
            $difference = (float) $task->quantity_kg - $oldQuantity;
            $batch->increment('quantity_kg', $difference);
        }
    }
}
