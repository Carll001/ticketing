<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskStep extends Model
{
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'task_id',
        'preset_step_id',
        'title',
        'description',
        'position',
        'status',
        'has_cost',
        'cost_amount',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'has_cost' => 'boolean',
        'cost_amount' => 'decimal:2',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(TaskStepField::class);
    }
}
