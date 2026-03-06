<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskPreset extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'description',
        'department_id',
        'creator_id',
    ];

    public function steps(): HasMany
    {
        return $this->hasMany(TaskPresetStep::class, 'preset_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
