<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskPresetField extends Model
{
    use HasUuids;

    protected $fillable = [
        'preset_step_id',
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
        return $this->belongsTo(TaskPresetStep::class, 'preset_step_id');
    }
}
