# Task-Harvest Integration Documentation

## Overview
This integration automatically syncs harvesting tasks with the Harvest (batch) module. When a harvesting task is marked as completed, the system automatically creates or updates a weekly harvest batch.

## Key Components

### 1. Database Migration
**File**: `database/migrations/2025_01_04_add_harvest_batch_id_to_tasks_table.php`
- Adds `harvest_batch_id` foreign key to `tasks` table
- Tracks which harvest batch each completed task belongs to
- Cascade: null on delete (if batch is deleted, task reference is cleared)

### 2. HarvestSyncService
**File**: `app/Services/HarvestSyncService.php`

Methods:
- **getWeekBoundaries($date)**: Determines Monday-Sunday week for a given date
- **findOrCreateBatchForDate($date)**: Finds existing batch for week, or creates new one
- **syncTaskToHarvest($task)**: Adds task quantity to harvest batch when marked completed
- **removeBatchQuantity($task)**: Subtracts task quantity from batch when status changes
- **syncQuantityChange($task, $oldQuantity)**: Updates batch when task quantity is modified

### 3. TaskObserver
**File**: `app/Observers/TaskObserver.php`

Automatically triggers on task changes:
- **updating()**: Detects status and quantity changes
- **deleting()**: Removes task quantity from batch before deletion

### 4. Model Updates
- **Task.php**: Added `harvestBatch()` relationship and observer registration
- **Harvest.php**: Added `tasks()` reverse relationship

## Workflow

### Scenario 1: Creating a Harvesting Task
```
1. User creates task: Harvesting, 25kg, work_date=Saturday Jan 4
2. Status initially: 'planned'
3. No sync happens yet (only happens on 'completed')
```

### Scenario 2: Marking Task as Completed
```
1. User changes status from 'planned' → 'completed'
2. TaskObserver.updating() detects status change
3. HarvestSyncService.syncTaskToHarvest() is called
4. System calculates week boundaries:
   - Task date: Saturday Jan 4
   - Week: Monday Jan 1 to Sunday Jan 7
5. System searches for existing batch in that week
6. If found: batch.quantity_kg += 25
7. If not found: Creates new batch with quantity_kg = 25
8. Task.harvest_batch_id = batch.id
```

### Scenario 3: Modifying Completed Task Quantity
```
1. Task is completed with 25kg, synced to batch
2. User changes quantity_kg: 25 → 30
3. TaskObserver.updating() detects quantity change
4. HarvestSyncService.syncQuantityChange() is called
5. batch.quantity_kg updated: +5 (30-25)
```

### Scenario 4: Changing Completed Task Back to Pending
```
1. Task is completed, quantity 25kg, synced to batch
2. User changes status: 'completed' → 'pending'
3. TaskObserver.updating() detects status change
4. HarvestSyncService.removeBatchQuantity() is called
5. batch.quantity_kg -= 25
6. If batch becomes 0 and was auto-created, batch is deleted
7. Task.harvest_batch_id = null
```

### Scenario 5: Deleting a Completed Task
```
1. Task is completed, synced to batch
2. User deletes task
3. TaskObserver.deleting() is triggered
4. HarvestSyncService.removeBatchQuantity() is called
5. Batch quantity is updated before task deletion
```

## Data Flow

```
Task Created (harvesting, 25kg, Jan 4, status=planned)
    ↓
User marks task completed
    ↓
TaskObserver detects status change
    ↓
HarvestSyncService.syncTaskToHarvest() called
    ↓
Calculate week: Jan 1-7
    ↓
Find or Create Harvest batch
    ↓
Update batch quantity: +25kg
    ↓
Link task to batch: task.harvest_batch_id = batch.id
    ↓
Batch now reflects in Harvest module for sales
```

## Usage Notes

1. **Only Harvesting Tasks Are Synced**: The service checks `task.type === 'harvesting'` before syncing
2. **Automatic Batch Creation**: No manual batch creation needed - system creates as tasks complete
3. **Week Definition**: Monday to Sunday (ISO week)
4. **Auto-Cleanup**: Empty auto-created batches are automatically deleted
5. **Quantity Tracking**: Batch quantity always reflects sum of all completed tasks for that week
6. **No Manual Sync Needed**: All syncing is automatic via observers

## Running the Migration

```bash
php artisan migrate
```

This will add the `harvest_batch_id` column to the tasks table.

## Testing the Integration

1. Create a harvesting task for a date (e.g., Saturday Jan 4)
2. Mark it as 'completed'
3. Check Harvest module - new batch should be auto-created for that week
4. Modify the task quantity - batch should update
5. Change task back to 'pending' - quantity should be removed from batch
6. Delete the task - batch should be updated/deleted accordingly
