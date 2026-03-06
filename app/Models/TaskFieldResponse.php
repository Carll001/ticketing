<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskFieldResponse extends Model
{
    use HasUuids;

    protected $table = 'task_field_responses';

    protected $fillable = [
        'task_step_field_id',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function stepField(): BelongsTo
    {
        return $this->belongsTo(TaskStepField::class, 'task_step_field_id');
    }
}
