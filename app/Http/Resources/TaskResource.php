<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $departmentAssigned = $this->whenLoaded('departmentAssigned', function (): ?array {
            if ($this->departmentAssigned === null) {
                return null;
            }

            return [
                'id' => $this->departmentAssigned->id,
                'name' => $this->departmentAssigned->name,
            ];
        });

        $assignedTo = $this->whenLoaded('assignedTo', function (): ?array {
            if ($this->assignedTo === null) {
                return null;
            }

            return [
                'id' => $this->assignedTo->id,
                'name' => $this->assignedTo->name,
            ];
        });

        $creator = $this->whenLoaded('creator', function (): ?array {
            if ($this->creator === null) {
                return null;
            }

            return [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ];
        });

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status ?? 'pending',
            'due_date' => optional($this->due_date)?->toDateString(),
            'step_order' => $this->step_order,
            'department_assigned' => $departmentAssigned,
            'assigned_to' => $assignedTo,
            'task_preset_id' => $this->task_preset_id,
            'cost_total' => $this->cost_total !== null ? (float) $this->cost_total : null,
            'creator' => $creator,
            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),

            'task_details' => [
                'title' => $this->title,
                'description' => $this->description,
                'due_date' => optional($this->due_date)?->toDateString(),
            ],
            'additional_details' => [
                'step_order' => $this->step_order,
                'department_assigned' => $departmentAssigned,
                'assigned_to' => $assignedTo,
                'task_preset_id' => $this->task_preset_id,
                'cost_total' => $this->cost_total !== null ? (float) $this->cost_total : null,
            ],
            'steps' => TaskStepResource::collection(
                $this->relationLoaded('steps') ? $this->steps : collect()
            )->resolve(),
        ];
    }
}
