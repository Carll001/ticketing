<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],

            // Task additional details
            'step_order' => ['required', Rule::in(['sequential', 'random'])],
            'task_type' => ['nullable', Rule::in(['preset', 'custom'])],
            'department_assigned_id' => ['nullable', 'uuid', 'exists:departments,id'],
            'assigned_to_user_id' => ['nullable', 'uuid', 'exists:users,id'],
            'task_preset_id' => ['nullable', 'uuid', 'exists:task_presets,id'],

            // Step details + step additional details
            'steps' => ['sometimes', 'array'],
            'steps.*.id' => ['nullable', 'uuid'],
            'steps.*.step_details' => ['required', 'array'],
            'steps.*.step_details.title' => ['required', 'string', 'max:255'],
            'steps.*.step_details.description' => ['nullable', 'string'],
            'steps.*.step_details.position' => ['nullable', 'integer', 'min:0'],
            'steps.*.step_details.status' => ['nullable', Rule::in(['pending', 'in_progress', 'done'])],

            'steps.*.additional_details' => ['nullable', 'array'],
            'steps.*.additional_details.has_cost' => ['nullable', 'boolean'],
            'steps.*.additional_details.cost_amount' => ['nullable', 'numeric', 'min:0'],

            'steps.*.fields' => ['nullable', 'array'],
            'steps.*.fields.*.label' => ['required', 'string', 'max:255'],
            'steps.*.fields.*.key' => ['nullable', 'string', 'max:255'],
            'steps.*.fields.*.type' => ['nullable', Rule::in(['text', 'textarea', 'number', 'date', 'select', 'checkbox', 'file', 'Textarea', 'Checkbox', 'SmallInput'])],
            'steps.*.fields.*.required' => ['nullable', 'boolean'],
            'steps.*.fields.*.options' => ['nullable', 'array'],
            'steps.*.fields.*.position' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
