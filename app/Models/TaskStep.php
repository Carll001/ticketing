<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskStep extends Model
{
    use HasUuids;

    protected $fillable = [
        'task_id',
        'preset_step_id',
        'claimed_by_user_id',
        'claimed_at',
        'title',
        'description',
        'allow_proof',
        'allow_comments',
        'has_cost',
        'expected_cost',
        'submitted_cost',
        'proof_text',
        'proof_type',
        'proof_file_path',
        'proof_file_name',
        'proof_file_mime',
        'proof_files',
        'position',
        'status',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'allow_proof' => 'boolean',
        'allow_comments' => 'boolean',
        'has_cost' => 'boolean',
        'expected_cost' => 'decimal:2',
        'submitted_cost' => 'decimal:2',
        'proof_files' => 'array',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(TaskStepField::class, 'task_step_id');
    }

    public function claimedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimed_by_user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskStepComment::class, 'task_step_id');
    }
}
