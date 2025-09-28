<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BulkStoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tasks' => 'required|array|min:1|max:100',
            'tasks.*.title' => 'required|string|max:255|min:3',
            'tasks.*.description' => 'nullable|string|max:1000',
            'tasks.*.priority' => 'nullable|in:low,medium,high',
            'tasks.*.status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'tasks.*.due_date' => 'nullable|date|after_or_equal:today',
            'tasks.*.tags' => 'nullable|array|max:10',
            'tasks.*.tags.*' => 'string|max:50|min:2'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'tasks.required' => 'At least one task is required.',
            'tasks.array' => 'Tasks must be provided as an array.',
            'tasks.min' => 'At least one task is required.',
            'tasks.max' => 'Cannot process more than 100 tasks at once.',

            'tasks.*.title.required' => 'Each task must have a title.',
            'tasks.*.title.min' => 'Task title must be at least 3 characters long.',
            'tasks.*.title.max' => 'Task title cannot exceed 255 characters.',
            'tasks.*.description.max' => 'Task description cannot exceed 1000 characters.',

            'tasks.*.priority.in' => 'Priority must be either low, medium, or high.',
            'tasks.*.status.in' => 'Status must be pending, in_progress, completed, or cancelled.',
            'tasks.*.due_date.after_or_equal' => 'Due date cannot be in the past.',
            'tasks.*.due_date.date' => 'Due date must be a valid date.',

            'tasks.*.tags.array' => 'Tags must be provided as an array.',
            'tasks.*.tags.max' => 'You cannot add more than 10 tags per task.',
            'tasks.*.tags.*.string' => 'Each tag must be a text string.',
            'tasks.*.tags.*.max' => 'Each tag cannot exceed 50 characters.',
            'tasks.*.tags.*.min' => 'Each tag must be at least 2 characters long.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'tasks' => 'tasks',
            'tasks.*.title' => 'task title',
            'tasks.*.description' => 'task description',
            'tasks.*.priority' => 'priority level',
            'tasks.*.status' => 'task status',
            'tasks.*.due_date' => 'due date',
            'tasks.*.tags' => 'tags',
            'tasks.*.tags.*' => 'tag'
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->toArray();
        $taskErrors = [];

        // Group errors by task index for better UX
        foreach ($errors as $field => $fieldErrors) {
            if (preg_match('/tasks\.(\d+)\.(.+)/', $field, $matches)) {
                $taskIndex = $matches[1];
                $fieldName = $matches[2];
                $taskErrors[$taskIndex][$fieldName] = $fieldErrors;
            } else {
                $taskErrors['general'][$field] = $fieldErrors;
            }
        }

        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Bulk validation failed',
            'errors' => $validator->errors(),
            'task_errors' => $taskErrors,
            'validation_summary' => [
                'total_errors' => $validator->errors()->count(),
                'failed_tasks' => count(array_filter($taskErrors, function($key) {
                    return is_numeric($key);
                }, ARRAY_FILTER_USE_KEY)),
                'total_tasks_submitted' => count(request()->input('tasks', []))
            ]
        ], 422));
    }
}
