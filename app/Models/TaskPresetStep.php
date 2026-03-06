<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskPresetStep extends Model
{
    use HasUuids;

    protected $fillable = [
        'preset_id',
        'title',
        'description',
        'allow_proof',
        'allow_comments',
        'has_cost',
        'expected_cost',
        'position',
    ];

    protected $casts = [
        'allow_proof' => 'boolean',
        'allow_comments' => 'boolean',
        'has_cost' => 'boolean',
        'expected_cost' => 'decimal:2',
    ];

    public function preset(): BelongsTo
    {
        return $this->belongsTo(TaskPreset::class, 'preset_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(TaskPresetField::class, 'preset_step_id');
    }
}
