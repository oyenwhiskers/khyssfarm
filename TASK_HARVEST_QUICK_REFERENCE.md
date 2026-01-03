# Task-Harvest Integration - Quick Reference

## What Happens Automatically

### When you CREATE a Task
```php
Task::create([
    'type' => 'harvesting',
    'status' => 'planned',  // ← No sync yet
    'quantity_kg' => 25,
    'work_date' => '2026-01-04',
]);
```
**Result**: Task is created but NOT synced to Harvest (because status is not 'completed')

---

### When you MARK A TASK AS COMPLETED
```php
$task->update(['status' => 'completed']);
```

**Automatic Actions**:
1. TaskObserver detects status change
2. HarvestSyncService.syncTaskToHarvest() triggers
3. Week is calculated: Jan 1-5, 2026 (Monday-Sunday containing Jan 4)
4. Search for Harvest batch for that week
5. **If found**: batch.quantity_kg += 25kg
6. **If not found**: Create new batch with 25kg
7. task.harvest_batch_id = batch.id

**Result**: Task quantity appears in Harvest module automatically ✓

---

### When you CHANGE COMPLETED TASK QUANTITY
```php
$task->update(['quantity_kg' => 30]); // was 25
```

**Automatic Actions**:
1. TaskObserver detects quantity change (while status = 'completed')
2. HarvestSyncService.syncQuantityChange() triggers
3. Difference calculated: 30 - 25 = +5kg
4. batch.quantity_kg += 5kg

**Result**: Batch total updated automatically ✓

---

### When you REVERT COMPLETED TASK TO PENDING
```php
$task->update(['status' => 'completed']); // back to 'planned'
```

**Automatic Actions**:
1. TaskObserver detects status change
2. HarvestSyncService.removeBatchQuantity() triggers
3. batch.quantity_kg -= 25kg
4. task.harvest_batch_id = null
5. **If batch is now empty AND was auto-created**: Batch deleted

**Result**: Task quantity removed from batch ✓

---

### When you DELETE A TASK
```php
$task->delete();
```

**Automatic Actions** (for completed tasks):
1. TaskObserver.deleting() triggers
2. HarvestSyncService.removeBatchQuantity() called first
3. Batch is updated/deleted as needed
4. Task is then deleted

**Result**: No orphaned quantities in batch ✓

---

## Database Structure

```
tasks table:
- id
- title
- type (harvesting, spraying, etc.)
- work_date
- status (planned, in_progress, completed) ← TRIGGERS SYNC
- quantity_kg (for harvesting) ← SYNCED TO BATCH
- rate
- cost
- description
- attachment_path
- harvest_batch_id (NEW) ← LINKS TO BATCH

harvests table:
- id
- harvest_date (Monday of the week)
- quantity_kg (SUM of all completed tasks for that week)
- variety
- notes (includes "Auto-created batch..." if system-created)
- field_location
- created_at
- updated_at
```

---

## Model Relationships

```php
// In Task model
$task->harvestBatch();  // Returns the Harvest batch this task is synced to

// In Harvest model
$harvest->tasks();      // Returns all tasks that contributed to this batch
```

---

## Service Methods (For Advanced Use)

```php
use App\Services\HarvestSyncService;

// Get week boundaries for a date
$boundaries = HarvestSyncService::getWeekBoundaries(Carbon::parse('2026-01-04'));
// Returns: ['week_start' => Monday, 'week_end' => Sunday]

// Find or create batch for a date
$batch = HarvestSyncService::findOrCreateBatchForDate(Carbon::parse('2026-01-04'));

// Manually sync a task (usually not needed - automatic via observer)
HarvestSyncService::syncTaskToHarvest($task);

// Manually remove task from batch (usually not needed)
HarvestSyncService::removeBatchQuantity($task);

// Handle quantity changes
HarvestSyncService::syncQuantityChange($task, $oldQuantity);
```

---

## Week Calculation Examples

```
Task Date: Friday, Jan 2, 2026
→ Week: Monday Jan 1 to Sunday Jan 5, 2026
→ Batch harvest_date: Jan 1, 2026

Task Date: Sunday, Jan 5, 2026
→ Week: Monday Jan 1 to Sunday Jan 5, 2026
→ Batch harvest_date: Jan 1, 2026
→ Both tasks synced to SAME batch ✓

Task Date: Monday, Jan 6, 2026
→ Week: Monday Jan 6 to Sunday Jan 12, 2026
→ Batch harvest_date: Jan 6, 2026
→ NEW batch (different week) ✓
```

---

## FAQ

**Q: What if I have multiple harvests on different dates in the same week?**
A: All are synced to ONE batch for that week. Batch total is the sum of all completed tasks.

**Q: Can I still manually create/edit Harvest batches?**
A: Yes! Manual batches won't be auto-deleted even if quantity becomes 0.

**Q: What about non-harvesting tasks (Spraying, Maintenance)?**
A: Only harvesting tasks trigger sync. Other types are ignored.

**Q: Can I undo a completed task?**
A: Yes, change status back to 'planned' and the quantity is automatically removed from the batch.

**Q: Do I need to run any code to sync?**
A: No! All syncing is automatic via Laravel Observers.

**Q: What if I delete a Harvest batch?**
A: All linked tasks have their harvest_batch_id set to null (no orphaned data).

---

## Troubleshooting

**Problem**: Task completed but Harvest batch not updated
- Check: Is task.type = 'harvesting'?
- Check: Is task.status = 'completed'?
- Check: Did you refresh the page to see the change?

**Problem**: Batch quantity seems wrong
- Check: Are all related tasks in 'completed' status?
- Check: Are tasks from different weeks (they create separate batches)?

**Problem**: Empty batch won't delete
- Note: Only auto-created batches delete when empty. Manual batches are preserved.

---

## Next: Cost Management Integration

After testing this integration, you may want to integrate with Cost Management:
- Link task costs to harvest batch
- Calculate profitability per batch
- Track worker costs vs harvest quantity

