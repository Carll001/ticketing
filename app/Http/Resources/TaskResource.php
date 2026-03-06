<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'task_preset_id' => $this->task_preset_id,
            'department_assigned_id' => $this->department_assigned_id,
            'creator' => $this->whenLoaded('creator', fn () => [
                'id' => $this->creator?->id,
                'name' => $this->creator?->name,
                'email' => $this->creator?->email,
                'role' => $this->creator?->role,
            ]),
            'steps_count' => $this->whenCounted('steps'),
            'last_step' => $this->whenLoaded('lastStep', fn () => [
                'id' => $this->lastStep?->id,
                'title' => $this->lastStep?->title,
                'status' => $this->lastStep?->status,
                'claimed_by' => $this->lastStep?->relationLoaded('claimedBy')
                    ? [
                        'id' => $this->lastStep?->claimedBy?->id,
                        'name' => $this->lastStep?->claimedBy?->name,
                    ]
                    : null,
            ]),
            'department_assigned' => $this->whenLoaded('departmentAssigned', function () {
                return [
                    'id' => $this->departmentAssigned?->id,
                    'name' => $this->departmentAssigned?->name,
                ];
            }),
            'steps' => $this->whenLoaded('steps', fn () => $this->steps
                ->sortBy('position')
                ->values()
                ->map(fn ($step) => [
                    'id' => $step->id,
                    'title' => $step->title,
                    'description' => $step->description,
                    'status' => $step->status,
                    'position' => $step->position,
                    'claimed_by_user_id' => $step->claimed_by_user_id,
                    'claimed_by' => $step->relationLoaded('claimedBy')
                        ? [
                            'id' => $step->claimedBy?->id,
                            'name' => $step->claimedBy?->name,
                        ]
                        : null,
                    'allow_proof' => (bool)($step->allow_proof ?? false),
                    'allow_comments' => (bool)($step->allow_comments ?? true),
                    'has_cost' => (bool)($step->has_cost ?? false),
                    'expected_cost' => $step->expected_cost,
                    'submitted_cost' => $step->submitted_cost,
                    'proof_text' => $step->proof_text,
                    'proof_type' => $step->proof_type,
                    'proof_file_name' => $step->proof_file_name,
                    'proof_file_mime' => $step->proof_file_mime,
                    'proof_file_url' => $step->proof_file_path ? Storage::disk('public')->url($step->proof_file_path) : null,
                    'proof_files' => collect($step->proof_files ?? [])->map(fn ($file) => [
                        'name' => data_get($file, 'name'),
                        'mime' => data_get($file, 'mime'),
                        'size' => data_get($file, 'size'),
                        'url' => data_get($file, 'path') ? Storage::disk('public')->url(data_get($file, 'path')) : null,
                    ])->values(),
                    'fields' => $step->relationLoaded('fields')
                        ? $step->fields
                            ->sortBy('position')
                            ->values()
                            ->map(fn ($field) => [
                                'id' => $field->id,
                                'label' => $field->label,
                                'type' => $field->type,
                                'required' => (bool)$field->required,
                                'placeholder' => null,
                                'value' => $field->relationLoaded('response')
                                    ? data_get($field->response?->value, 'value')
                                    : null,
                                'position' => $field->position,
                            ])->values()
                        : [],
                    'comments' => $step->relationLoaded('comments')
                        ? $step->comments
                            ->sortBy('created_at')
                            ->values()
                            ->map(fn ($comment) => [
                                'id' => $comment->id,
                                'message' => $comment->message,
                                'created_at' => $comment->created_at,
                                'user' => $comment->relationLoaded('user')
                                    ? [
                                        'id' => $comment->user?->id,
                                        'name' => $comment->user?->name,
                                    ]
                                    : null,
                            ])->values()
                        : [],
                ])
                ->values()),
        ];
    }
}
