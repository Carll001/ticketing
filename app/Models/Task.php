<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Task extends Model
{
    use HasUuids;

    protected $fillable = [
        'title',
        'description',
        'cost_total',
        'task_preset_id',
        'department_assigned_id',
        'creator_id',
    ];

    public function departmentAssigned(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_assigned_id');
    }

    public function preset(): BelongsTo
    {
        return $this->belongsTo(TaskPreset::class, 'task_preset_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(TaskStep::class, 'task_id');
    }

    public function lastStep(): HasOne
    {
        return $this->hasOne(TaskStep::class, 'task_id')->orderByDesc('position');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
