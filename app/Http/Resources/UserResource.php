<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'departments' => DepartmentResource::collection($this->whenLoaded('departments')),
            'permissions' => $this->whenLoaded('permissions', fn() => $this->permissions->map(fn($p) => ['name' => $p->name])),
        ];
    }
}
