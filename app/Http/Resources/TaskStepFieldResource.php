<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskStepFieldResource extends JsonResource
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
            'label' => $this->label,
            'key' => $this->key,
            'type' => $this->type,
            'required' => (bool) $this->required,
            'options' => $this->options,
            'position' => (int) $this->position,
        ];
    }
}

