<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskStepField extends Model
{
    use HasUuids;

    /**
     * @var list<string>
     */
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

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'required' => 'boolean',
        'options' => 'array',
    ];

    public function step(): BelongsTo
    {
        return $this->belongsTo(TaskStep::class, 'task_step_id');
    }
}
