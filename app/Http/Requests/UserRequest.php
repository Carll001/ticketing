<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'role' => $this->role ?? 'staff',
        ]);
    }

    public function rules(): array
    {
        /** @var \App\Models\User|null $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],

            'password' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'confirmed',
                'min:8',
            ],

            // adjust if your departments table is uuid/int
            'department_ids'   => ['nullable', 'array'],
            'department_ids.*' => ['exists:departments,id'],

            // Spatie role name(s)
            'role' => ['nullable', 'string'],

            // Spatie permission names
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }
}
