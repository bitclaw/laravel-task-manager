<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReorderTasksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['nullable', Rule::exists('projects', 'id')],
            'task_ids' => ['required', 'array', 'min:1'],
            'task_ids.*' => ['integer', Rule::exists('tasks', 'id')],
        ];
    }
}
