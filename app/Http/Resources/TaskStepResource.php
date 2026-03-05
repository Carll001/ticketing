<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskStepResource extends JsonResource
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
            'step_details' => [
                'title' => $this->title,
                'description' => $this->description,
                'position' => (int) $this->position,
                'status' => $this->status,
            ],
            'additional_details' => [
                'has_cost' => (bool) $this->has_cost,
                'cost_amount' => $this->cost_amount !== null ? (float) $this->cost_amount : null,
            ],
            'fields' => TaskStepFieldResource::collection(
                $this->relationLoaded('fields') ? $this->fields : collect()
            )->resolve(),
        ];
    }
}
