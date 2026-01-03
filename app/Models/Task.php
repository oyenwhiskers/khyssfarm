<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Observers\TaskObserver;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'work_date',
        'status',
        'quantity_kg',
        'tree_count',
        'rate',
        'cost',
        'description',
        'attachment_path',
        'harvest_batch_id',
    ];

    protected $casts = [
        'work_date' => 'date',
        'cost' => 'decimal:2',
        'quantity_kg' => 'decimal:2',
        'tree_count' => 'integer',
        'rate' => 'decimal:2',
    ];

    public const STATUSES = [
        'planned' => 'Planned',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
    ];

    public const TYPES = [
        'harvesting' => 'Harvesting',
        'spraying' => 'Spraying',
        'grass_cut' => 'Grass Cut',
        'sanitation' => 'Sanitation',
        'maintenance' => 'Maintenance',
        'manuring' => 'Manuring',
        'other' => 'Other',
    ];

    /**
     * Register observers
     */
    protected static function boot()
    {
        parent::boot();
        self::observe(TaskObserver::class);
    }

    public function workers()
    {
        return $this->belongsToMany(Worker::class)->withTimestamps();
    }

    /**
     * Relationship to Harvest batch
     * A harvesting task belongs to a harvest batch
     */
    public function harvestBatch()
    {
        return $this->belongsTo(Harvest::class, 'harvest_batch_id');
    }

    /**
     * Relationship to Cost records
     * A task can have multiple cost records associated with it
     */
    public function costs()
    {
        return $this->hasMany(Cost::class);
    }
}

