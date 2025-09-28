<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'tags'
    ];

    protected $casts = [
        'due_date' => 'date',
        'tags' => 'array'
    ];

    protected $attributes = [
        'priority' => 'medium',
        'status' => 'pending'
    ];

    // Validation rules
    public static function validationRules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'due_date' => 'nullable|date|after_or_equal:today',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'
        ];
    }

    // Validation rules for bulk operations
    public static function bulkValidationRules()
    {
        return [
            'tasks' => 'required|array|min:1|max:100',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.description' => 'nullable|string|max:1000',
            'tasks.*.priority' => 'nullable|in:low,medium,high',
            'tasks.*.status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'tasks.*.due_date' => 'nullable|date',
            'tasks.*.tags' => 'nullable|array',
            'tasks.*.tags.*' => 'string|max:50'
        ];
    }

    // Scopes for filtering
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('due_date', '<=', now()->addDays($days))
                    ->where('due_date', '>=', now());
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'completed');
    }

    // Accessors
    protected function priorityBadge(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->priority) {
                'low' => ['color' => 'green', 'label' => 'Low'],
                'medium' => ['color' => 'yellow', 'label' => 'Medium'],
                'high' => ['color' => 'red', 'label' => 'High'],
            }
        );
    }

    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'pending' => ['color' => 'gray', 'label' => 'Pending'],
                'in_progress' => ['color' => 'blue', 'label' => 'In Progress'],
                'completed' => ['color' => 'green', 'label' => 'Completed'],
                'cancelled' => ['color' => 'red', 'label' => 'Cancelled'],
            }
        );
    }
}
