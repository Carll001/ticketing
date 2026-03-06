<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskPresetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'department_id' => $this->department_id,
            'department' => $this->whenLoaded('department', fn () => [
                'id' => $this->department?->id,
                'name' => $this->department?->name,
            ]),
            'steps' => $this->whenLoaded('steps', fn () => $this->steps
                ->sortBy('position')
                ->values()
                ->map(fn ($step) => [
                    'id' => $step->id,
                    'title' => $step->title,
                    'description' => $step->description,
                    'allow_proof' => (bool)($step->allow_proof ?? false),
                    'allow_comments' => (bool)($step->allow_comments ?? true),
                    'has_cost' => (bool)($step->has_cost ?? false),
                    'expected_cost' => $step->expected_cost,
                    'position' => $step->position,
                    'fields' => $step->relationLoaded('fields')
                        ? $step->fields
                            ->sortBy('position')
                            ->values()
                            ->map(fn ($field) => [
                                'id' => $field->id,
                                'label' => $field->label,
                                'type' => $field->type,
                                'required' => (bool) $field->required,
                                'placeholder' => null,
                                'position' => $field->position,
                            ])
                            ->values()
                        : [],
                ])
                ->values()),
        ];
    }
}
