<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

/**
 * @OA\Info(
 *     title="Task Management API",
 *     version="1.0.0",
 *     description="Comprehensive Task Management System with Multiple Data Insertion Capabilities",
 *     @OA\Contact(
 *         email="developer@taskmanagement.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Development API Server"
 * )
 *
 * @OA\Schema(
 *     schema="Task",
 *     type="object",
 *     title="Task",
 *     description="Task model",
 *     required={"title"},
 *     @OA\Property(property="id", type="integer", format="int64", description="Task ID", example=1),
 *     @OA\Property(property="title", type="string", maxLength=255, description="Task title", example="Complete project documentation"),
 *     @OA\Property(property="description", type="string", maxLength=1000, description="Task description", example="Write comprehensive documentation for the project"),
 *     @OA\Property(property="priority", type="string", enum={"low","medium","high"}, description="Task priority", example="medium"),
 *     @OA\Property(property="status", type="string", enum={"pending","in_progress","completed","cancelled"}, description="Task status", example="pending"),
 *     @OA\Property(property="due_date", type="string", format="date", description="Task due date", example="2024-12-31"),
 *     @OA\Property(
 *         property="tags",
 *         type="array",
 *         @OA\Items(type="string"),
 *         description="Task tags",
 *         example={"urgent", "documentation"}
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp")
 * )
 *
 * @OA\Schema(
 *     schema="ApiResponse",
 *     type="object",
 *     @OA\Property(property="status", type="string", description="Response status", example="success"),
 *     @OA\Property(property="message", type="string", description="Response message"),
 *     @OA\Property(property="data", type="object", description="Response data"),
 *     @OA\Property(property="execution_time_ms", type="number", description="Execution time in milliseconds", example=45.23)
 * )
 *
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     @OA\Property(property="status", type="string", example="error"),
 *     @OA\Property(property="message", type="string", example="Validation failed"),
 *     @OA\Property(property="errors", type="object", description="Field-specific validation errors")
 * )
 */
class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/tasks",
     *     summary="Get all tasks",
     *     description="Retrieve a paginated list of tasks with optional filtering and sorting",
     *     operationId="getTasks",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by task status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"all","pending","in_progress","completed","cancelled"})
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Filter by task priority",
     *         required=false,
     *         @OA\Schema(type="string", enum={"all","low","medium","high"})
     *     ),
     *     @OA\Parameter(
     *         name="due_soon",
     *         in="query",
     *         description="Show tasks due soon",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="overdue",
     *         in="query",
     *         description="Show overdue tasks only",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort field",
     *         required=false,
     *         @OA\Schema(type="string", enum={"created_at","due_date","priority","title"}, default="created_at")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc","desc"}, default="desc")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tasks retrieved successfully",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         @OA\Property(
     *                             property="data",
     *                             type="array",
     *                             @OA\Items(ref="#/components/schemas/Task")
     *                         ),
     *                         @OA\Property(property="current_page", type="integer"),
     *                         @OA\Property(property="last_page", type="integer"),
     *                         @OA\Property(property="total", type="integer")
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
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

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/tasks",
     *     summary="Create a new task",
     *     description="Create a single task with validation",
     *     operationId="createTask",
     *     tags={"Tasks"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Task data",
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", maxLength=255, example="Complete project documentation"),
     *             @OA\Property(property="description", type="string", maxLength=1000, example="Write comprehensive documentation"),
     *             @OA\Property(property="priority", type="string", enum={"low","medium","high"}, example="medium"),
     *             @OA\Property(property="status", type="string", enum={"pending","in_progress","completed","cancelled"}, example="pending"),
     *             @OA\Property(property="due_date", type="string", format="date", example="2024-12-31"),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"urgent", "documentation"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                 @OA\Schema(
     *                     @OA\Property(property="data", ref="#/components/schemas/Task")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
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

    /**
     * Store multiple resources in storage (Bulk Insert).
     *
     * @OA\Post(
     *     path="/tasks/bulk",
     *     summary="Create multiple tasks",
     *     description="Create multiple tasks at once using optimized bulk insert operations",
     *     operationId="bulkCreateTasks",
     *     tags={"Tasks"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Bulk task data",
     *         @OA\JsonContent(
     *             required={"tasks"},
     *             @OA\Property(
     *                 property="tasks",
     *                 type="array",
     *                 minItems=1,
     *                 maxItems=100,
     *                 @OA\Items(
     *                     type="object",
     *                     required={"title"},
     *                     @OA\Property(property="title", type="string", maxLength=255),
     *                     @OA\Property(property="description", type="string", maxLength=1000),
     *                     @OA\Property(property="priority", type="string", enum={"low","medium","high"}),
     *                     @OA\Property(property="status", type="string", enum={"pending","in_progress","completed","cancelled"}),
     *                     @OA\Property(property="due_date", type="string", format="date"),
     *                     @OA\Property(
     *                         property="tags",
     *                         type="array",
     *                         @OA\Items(type="string")
     *                     )
     *                 )
     *             ),
     *             example={
     *                 "tasks": {
     *                     {
     *                         "title": "Complete project documentation",
     *                         "description": "Write comprehensive docs",
     *                         "priority": "high",
     *                         "status": "pending",
     *                         "due_date": "2024-01-15",
     *                         "tags": {"documentation", "urgent"}
     *                     },
     *                     {
     *                         "title": "Review pull requests",
     *                         "description": "Review pending PRs",
     *                         "priority": "medium",
     *                         "tags": {"review", "development"}
     *                     }
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tasks created successfully",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="data",
     *                         type="array",
     *                         @OA\Items(ref="#/components/schemas/Task")
     *                     ),
     *                     @OA\Property(property="count", type="integer", description="Number of tasks created"),
     *                     @OA\Property(
     *                         property="performance_info",
     *                         type="object",
     *                         @OA\Property(property="bulk_insert_used", type="boolean"),
     *                         @OA\Property(property="tasks_per_second", type="number")
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ValidationError"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="task_errors",
     *                         type="object",
     *                         description="Errors grouped by task index"
     *                     )
     *                 )
     *             }
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
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

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/tasks/{id}",
     *     summary="Get a specific task",
     *     description="Retrieve a single task by its ID",
     *     operationId="getTask",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Task ID",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task retrieved successfully",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                 @OA\Schema(
     *                     @OA\Property(property="data", ref="#/components/schemas/Task")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     *
     * @param Task $task
     * @return JsonResponse
     */
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

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/tasks/{id}",
     *     summary="Update a task",
     *     description="Update an existing task with validation",
     *     operationId="updateTask",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Task ID",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated task data",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task updated successfully",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                 @OA\Schema(
     *                     @OA\Property(property="data", ref="#/components/schemas/Task")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     *
     * @param Request $request
     * @param Task $task
     * @return JsonResponse
     */
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

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/tasks/{id}",
     *     summary="Delete a task",
     *     description="Delete an existing task",
     *     operationId="deleteTask",
     *     tags={"Tasks"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Task ID",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task deleted successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function destroy(Task $task): JsonResponse
    {
        try {
            $taskTitle = $task->title;
            $task->delete();

            return response()->json([
                'status' => 'success',
                'message' => "Task '{$taskTitle}' deleted successfully"
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete task',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get task statistics
     *
     * @OA\Get(
     *     path="/tasks-stats",
     *     summary="Get task statistics",
     *     description="Retrieve comprehensive statistics about all tasks",
     *     operationId="getTaskStatistics",
     *     tags={"Tasks"},
     *     @OA\Response(
     *         response=200,
     *         description="Statistics retrieved successfully",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         @OA\Property(property="total_tasks", type="integer"),
     *                         @OA\Property(
     *                             property="by_status",
     *                             type="object",
     *                             @OA\Property(property="pending", type="integer"),
     *                             @OA\Property(property="in_progress", type="integer"),
     *                             @OA\Property(property="completed", type="integer"),
     *                             @OA\Property(property="cancelled", type="integer")
     *                         ),
     *                         @OA\Property(
     *                             property="by_priority",
     *                             type="object",
     *                             @OA\Property(property="low", type="integer"),
     *                             @OA\Property(property="medium", type="integer"),
     *                             @OA\Property(property="high", type="integer")
     *                         ),
     *                         @OA\Property(property="overdue_tasks", type="integer"),
     *                         @OA\Property(property="due_soon", type="integer")
     *                     )
     *                 )
     *             }
     *         )
     *     )
     * )
     *
     * @return JsonResponse
     */
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
