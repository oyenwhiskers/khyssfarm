<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\Harvest;
use App\Models\Worker;
use Carbon\Carbon;

class TaskHarvestIntegrationTest extends TestCase
{
    /**
     * Test that completing a harvesting task creates a harvest batch
     */
    public function test_completing_harvesting_task_creates_batch(): void
    {
        // Create a worker
        $worker = Worker::factory()->create();

        // Create a harvesting task for a specific date
        $task = Task::create([
            'title' => 'Weekly Harvest',
            'type' => 'harvesting',
            'work_date' => '2026-01-04', // Saturday
            'status' => 'planned',
            'quantity_kg' => 25.5,
            'rate' => 5,
            'cost' => 127.5,
            'description' => 'Test harvest',
        ]);

        $task->workers()->attach($worker->id);

        // Verify no harvest batch exists yet
        $this->assertEquals(0, Harvest::count());

        // Mark task as completed
        $task->update(['status' => 'completed']);

        // Verify harvest batch was created
        $this->assertEquals(1, Harvest::count());

        $batch = Harvest::first();
        $this->assertEquals(25.5, $batch->quantity_kg);
        $this->assertEquals($batch->id, $task->refresh()->harvest_batch_id);

        // Verify batch is for the correct week (Jan 1-7, starting Monday Dec 30 or Jan 1)
        $weekStart = Carbon::parse('2026-01-04')->startOfWeek(Carbon::MONDAY);
        $this->assertTrue($batch->harvest_date->equalTo($weekStart));
    }

    /**
     * Test that changing quantity of completed task updates batch
     */
    public function test_changing_completed_task_quantity_updates_batch(): void
    {
        $worker = Worker::factory()->create();

        $task = Task::create([
            'title' => 'Harvest Task',
            'type' => 'harvesting',
            'work_date' => '2026-01-04',
            'status' => 'completed',
            'quantity_kg' => 25,
            'rate' => 5,
            'cost' => 125,
        ]);

        $task->workers()->attach($worker->id);

        // Batch should be auto-created
        $batch = Harvest::first();
        $this->assertEquals(25, $batch->quantity_kg);

        // Change quantity
        $task->update(['quantity_kg' => 30]);

        // Batch should be updated
        $batch->refresh();
        $this->assertEquals(30, $batch->quantity_kg);
    }

    /**
     * Test that marking completed task back to pending removes from batch
     */
    public function test_changing_completed_to_pending_removes_from_batch(): void
    {
        $worker = Worker::factory()->create();

        $task = Task::create([
            'title' => 'Harvest Task',
            'type' => 'harvesting',
            'work_date' => '2026-01-04',
            'status' => 'completed',
            'quantity_kg' => 25,
            'rate' => 5,
            'cost' => 125,
        ]);

        $task->workers()->attach($worker->id);

        $batch = Harvest::first();
        $this->assertEquals(25, $batch->quantity_kg);

        // Change status back to pending
        $task->update(['status' => 'planned']);

        // Batch should be deleted if empty and auto-created
        $this->assertEquals(0, Harvest::count());
    }

    /**
     * Test that multiple tasks in same week are accumulated
     */
    public function test_multiple_tasks_same_week_accumulate(): void
    {
        $worker = Worker::factory()->create();

        // Create two tasks in same week
        $task1 = Task::create([
            'title' => 'Harvest 1',
            'type' => 'harvesting',
            'work_date' => '2026-01-02', // Friday
            'status' => 'completed',
            'quantity_kg' => 20,
            'rate' => 5,
            'cost' => 100,
        ]);

        $task1->workers()->attach($worker->id);

        $task2 = Task::create([
            'title' => 'Harvest 2',
            'type' => 'harvesting',
            'work_date' => '2026-01-04', // Sunday
            'status' => 'completed',
            'quantity_kg' => 15,
            'rate' => 5,
            'cost' => 75,
        ]);

        $task2->workers()->attach($worker->id);

        // Should have one batch with accumulated quantity
        $this->assertEquals(1, Harvest::count());

        $batch = Harvest::first();
        $this->assertEquals(35, $batch->quantity_kg); // 20 + 15
    }

    /**
     * Test that non-harvesting tasks are not synced
     */
    public function test_non_harvesting_tasks_not_synced(): void
    {
        $worker = Worker::factory()->create();

        $task = Task::create([
            'title' => 'Spraying Task',
            'type' => 'spraying',
            'work_date' => '2026-01-04',
            'status' => 'completed',
            'rate' => 20,
            'cost' => 20,
        ]);

        $task->workers()->attach($worker->id);

        // No harvest batch should be created
        $this->assertEquals(0, Harvest::count());
    }
}
