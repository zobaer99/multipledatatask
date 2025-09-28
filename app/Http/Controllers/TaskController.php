<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Exception;

class TaskController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $query = Task::query();

            // Apply filters
            if ($request->has('status') && $request->status !== 'all') {
                $query->byStatus($request->status);
            }

            if ($request->has('priority') && $request->priority !== 'all') {
                $query->byPriority($request->priority);
            }

            if ($request->has('due_soon') && $request->due_soon) {
                $query->dueSoon($request->get('days', 7));
            }

            if ($request->has('overdue') && $request->overdue) {
                $query->overdue();
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $tasks = $query->paginate($perPage);

            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'status' => 'success',
                'data' => $tasks,
                'execution_time_ms' => $executionTime,
                'filters_applied' => $request->only(['status', 'priority', 'due_soon', 'overdue']),
                'total_tasks' => $tasks->total()
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch tasks',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    public function store(Request $request): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $validator = Validator::make($request->all(), Task::validationRules());

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $task = Task::create($validator->validated());
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'status' => 'success',
                'message' => 'Task created successfully',
                'data' => $task->fresh(),
                'execution_time_ms' => $executionTime
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create task',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function bulkStore(Request $request): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $validator = Validator::make($request->all(), Task::bulkValidationRules());

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $tasksData = $request->input('tasks');
            $createdTasks = [];

            DB::beginTransaction();

            try {
                // Process each task
                foreach ($tasksData as $index => $taskData) {
                    // Set defaults if not provided
                    $taskData['priority'] = $taskData['priority'] ?? 'medium';
                    $taskData['status'] = $taskData['status'] ?? 'pending';

                    // Convert tags array to JSON for database storage
                    if (isset($taskData['tags']) && is_array($taskData['tags'])) {
                        $taskData['tags'] = json_encode($taskData['tags']);
                    } else {
                        $taskData['tags'] = json_encode([]);
                    }

                    // Convert due_date to proper format if provided
                    if (isset($taskData['due_date']) && !empty($taskData['due_date'])) {
                        $taskData['due_date'] = Carbon::parse($taskData['due_date'])->format('Y-m-d');
                    } else {
                        $taskData['due_date'] = null;
                    }

                    // Add timestamps
                    $taskData['created_at'] = now();
                    $taskData['updated_at'] = now();

                    $createdTasks[] = $taskData;
                }

                // Bulk insert for better performance
                Task::insert($createdTasks);

                // Get the created tasks (last n tasks)
                $insertedTasks = Task::latest('id')
                    ->take(count($createdTasks))
                    ->get()
                    ->reverse()
                    ->values();

                DB::commit();

                $executionTime = round((microtime(true) - $startTime) * 1000, 2);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Tasks created successfully',
                    'data' => $insertedTasks,
                    'count' => count($insertedTasks),
                    'execution_time_ms' => $executionTime,
                    'performance_info' => [
                        'bulk_insert_used' => true,
                        'tasks_per_second' => round(count($insertedTasks) / ($executionTime / 1000), 2)
                    ]
                ], 201);

            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create tasks',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function show(Task $task): JsonResponse
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => $task
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch task',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $validator = Validator::make($request->all(), Task::validationRules());

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $task->update($validator->validated());
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'status' => 'success',
                'message' => 'Task updated successfully',
                'data' => $task->fresh(),
                'execution_time_ms' => $executionTime
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update task',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }


    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_tasks' => Task::count(),
                'by_status' => [
                    'pending' => Task::byStatus('pending')->count(),
                    'in_progress' => Task::byStatus('in_progress')->count(),
                    'completed' => Task::byStatus('completed')->count(),
                    'cancelled' => Task::byStatus('cancelled')->count()
                ],
                'by_priority' => [
                    'low' => Task::byPriority('low')->count(),
                    'medium' => Task::byPriority('medium')->count(),
                    'high' => Task::byPriority('high')->count()
                ],
                'overdue_tasks' => Task::overdue()->count(),
                'due_soon' => Task::dueSoon()->count()
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
