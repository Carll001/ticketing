<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TaskStepField extends Model
{
    use HasUuids;

    protected $fillable = [
        'task_step_id',
        'preset_field_id',
        'label',
        'key',
        'type',
        'required',
        'options',
        'position',
    ];

    protected $casts = [
        'required' => 'boolean',
        'options' => 'array',
    ];

    public function step(): BelongsTo
    {
        return $this->belongsTo(TaskStep::class, 'task_step_id');
    }

    public function response(): HasOne
    {
        return $this->hasOne(TaskFieldResponse::class, 'task_step_field_id');
    }
}
