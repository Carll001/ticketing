<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'step_order',
        'task_preset_id',
        'department_assigned_id',
        'assigned_to_user_id',
        'creator_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'date',
        'cost_total' => 'decimal:2',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function departmentAssigned(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_assigned_id');
    }

    public function taskPreset(): BelongsTo
    {
        return $this->belongsTo(TaskPreset::class, 'task_preset_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(TaskStep::class);
    }
}
