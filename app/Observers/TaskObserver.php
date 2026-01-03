<?php

namespace App\Observers;

use App\Models\Task;
use App\Services\HarvestSyncService;
use App\Services\CostSyncService;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     * If task is harvesting and created with 'completed' status, sync immediately
     */
    public function created(Task $task): void
    {
        // Note: Cost sync is handled in TaskController after workers are attached
        
        // If task is harvesting and completed, sync with harvest batch
        if ($task->type === 'harvesting' && $task->status === 'completed') {
            HarvestSyncService::syncTaskToHarvest($task);
        }
    }

    /**
     * Handle the Task "updating" event.
     * Detect status changes and sync with Harvest module
     */
    public function updating(Task $task): void
    {
        // Note: Cost sync is handled in TaskController after workers are attached

        // Get the original status before changes
        $originalStatus = $task->getOriginal('status');
        $newStatus = $task->status;
        $originalQuantity = (float) $task->getOriginal('quantity_kg');

        // If status changed TO 'completed', sync with harvest
        if ($originalStatus !== 'completed' && $newStatus === 'completed') {
            HarvestSyncService::syncTaskToHarvest($task);
        }

        // If status changed FROM 'completed' to something else, remove from harvest
        if ($originalStatus === 'completed' && $newStatus !== 'completed') {
            HarvestSyncService::removeBatchQuantity($task);
        }

        // If task is completed and quantity changed, update harvest batch
        if ($newStatus === 'completed' && $originalQuantity !== (float) $task->quantity_kg) {
            HarvestSyncService::syncQuantityChange($task, $originalQuantity);
        }
    }

    /**
     * Handle the Task "deleting" event.
     * Remove from harvest batch and cost if synced
     */
    public function deleting(Task $task): void
    {
        // If task is harvesting and synced to a harvest batch, remove it
        if ($task->type === 'harvesting' && $task->harvest_batch_id) {
            HarvestSyncService::removeBatchQuantity($task, true);
        }

        // Remove associated cost records
        CostSyncService::removeCost($task);
    }
}
