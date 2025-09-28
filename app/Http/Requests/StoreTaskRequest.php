<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTaskRequest extends FormRequest
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
            'title' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:1000',
            'priority' => 'nullable|in:low,medium,high',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'due_date' => 'nullable|date|after_or_equal:today',
            'tags' => 'nullable|array|max:10',
            'tags.*' => 'string|max:50|min:2'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required and cannot be empty.',
            'title.min' => 'Task title must be at least 3 characters long.',
            'title.max' => 'Task title cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'priority.in' => 'Priority must be either low, medium, or high.',
            'status.in' => 'Status must be pending, in_progress, completed, or cancelled.',
            'due_date.after_or_equal' => 'Due date cannot be in the past.',
            'due_date.date' => 'Due date must be a valid date.',
            'tags.array' => 'Tags must be provided as an array.',
            'tags.max' => 'You cannot add more than 10 tags.',
            'tags.*.string' => 'Each tag must be a text string.',
            'tags.*.max' => 'Each tag cannot exceed 50 characters.',
            'tags.*.min' => 'Each tag must be at least 2 characters long.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'task title',
            'description' => 'task description',
            'priority' => 'priority level',
            'status' => 'task status',
            'due_date' => 'due date',
            'tags' => 'tags',
            'tags.*' => 'tag'
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
            'validation_summary' => [
                'total_errors' => $validator->errors()->count(),
                'failed_fields' => array_keys($validator->errors()->toArray())
            ]
        ], 422));
    }
}
