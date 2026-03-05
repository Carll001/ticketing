<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

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
        'creator_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function departmentAssigned(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_assigned_id');
    }
}
