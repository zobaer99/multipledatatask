@extends('layouts.app')

@section('title', 'Task Management - Multiple Data Insertion')

@section('styles')
<style>
    .task-form-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .repeater-item {
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        position: relative;
        transition: all 0.3s ease;
    }

    .repeater-item:hover {
        border-color: var(--primary-color);
        background: #f0f9ff;
    }

    .repeater-item.completed {
        border-color: var(--success-color);
        background: #f0fdf4;
    }

    .remove-task-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--danger-color);
        color: white;
        border: none;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .remove-task-btn:hover {
        background: #dc2626;
        transform: scale(1.1);
    }

    .priority-badge {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .priority-low { background: #dcfce7; color: #166534; }
        .priority-medium { background: #fef3c7; color: #92400e; }
        .priority-high { background: #fee2e2; color: #991b1b; }

        .status-badge {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .status-pending { background: #f3f4f6; color: #374151; }
        .status-in_progress { background: #dbeafe; color: #1e40af; }
        .status-completed { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        .add-task-btn {
            background: linear-gradient(135deg, var(--success-color), #059669);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .add-task-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .submit-all-btn {
            background: linear-gradient(135deg, var(--primary-color), #1e40af);
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .submit-all-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }

        .submit-all-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -5px rgba(0, 0, 0, 0.15);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            display: none;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .task-counter {
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .performance-info {
            background: #e0f2fe;
            border: 1px solid #0284c7;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .tag-input {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 0.5rem;
            width: 100%;
        }

        .tag-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .tag-item {
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .tag-remove {
            cursor: pointer;
            font-weight: bold;
        }
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="page-title">
                    <i class="fas fa-tasks me-3"></i>
                    Task Management
                </h1>
                <p class="page-subtitle mb-0">Multiple Data Insertion with Dynamic Repeater Fields</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <div class="task-counter text-white" id="taskCounter">
                        <span class="stat-number" style="font-size: 2rem;">0</span>
                        <div class="stat-label">Tasks Ready</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
        <!-- Statistics Row -->
        <div class="row mb-4" id="statsRow">
            <!-- Stats will be loaded here -->
        </div>

        <!-- Task Form Container -->
        <div class="task-form-container">
            <div class="row align-items-center mb-3">
                <div class="col">
                    <h3 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        Add Multiple Tasks
                    </h3>
                    <p class="text-muted mb-0">Create multiple tasks at once without page reload</p>
                </div>
                <div class="col-auto">
                    <button type="button" class="add-task-btn" id="addTaskBtn">
                        <i class="fas fa-plus"></i>
                        Add Another Task
                    </button>
                </div>
            </div>

            <form id="taskForm">
                <div id="tasksContainer">
                    <!-- Initial task form will be added here -->
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-outline-secondary" id="clearAllBtn">
                            <i class="fas fa-trash"></i>
                            Clear All
                        </button>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="submit" class="submit-all-btn" id="submitAllBtn">
                            <i class="fas fa-paper-plane me-2"></i>
                            Submit All Tasks
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Performance Information -->
        <div id="performanceInfo" style="display: none;"></div>

        <!-- Recent Tasks -->
        <div class="task-card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Recent Tasks
                        </h4>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="refreshTasksBtn">
                            <i class="fas fa-sync-alt"></i>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="recentTasksList">
                    <!-- Recent tasks will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        $(document).ready(function() {
            let taskCounter = 0;

            // CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Add initial task form
            addTaskForm();

            // Load statistics and recent tasks
            loadStatistics();
            loadRecentTasks();

            // Add task button
            $('#addTaskBtn').click(function() {
                addTaskForm();
                updateTaskCounter();
            });

            // Clear all tasks
            $('#clearAllBtn').click(function() {
                if (confirm('Are you sure you want to clear all tasks?')) {
                    $('#tasksContainer').empty();
                    taskCounter = 0;
                    updateTaskCounter();
                }
            });

            // Form submission
            $('#taskForm').submit(function(e) {
                e.preventDefault();
                submitAllTasks();
            });

            // Refresh tasks
            $('#refreshTasksBtn').click(function() {
                loadRecentTasks();
            });

            function addTaskForm() {
                taskCounter++;
                const taskId = 'task_' + taskCounter;

                const taskForm = `
                    <div class="repeater-item fade-in" data-task-id="${taskId}">
                        <button type="button" class="remove-task-btn" onclick="removeTask('${taskId}')">
                            <i class="fas fa-times"></i>
                        </button>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-heading me-1"></i>
                                    Task Title *
                                </label>
                                <input type="text" class="form-control" name="tasks[${taskCounter}][title]"
                                       placeholder="Enter task title" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    Priority
                                </label>
                                <select class="form-select" name="tasks[${taskCounter}][priority]">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-align-left me-1"></i>
                                    Description
                                </label>
                                <textarea class="form-control" name="tasks[${taskCounter}][description]"
                                         rows="3" placeholder="Enter task description"></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-calendar me-1"></i>
                                    Due Date
                                </label>
                                <input type="date" class="form-control" name="tasks[${taskCounter}][due_date]"
                                       min="${new Date().toISOString().split('T')[0]}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-flag me-1"></i>
                                    Status
                                </label>
                                <select class="form-select" name="tasks[${taskCounter}][status]">
                                    <option value="pending" selected>Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-tags me-1"></i>
                                    Tags
                                </label>
                                <input type="text" class="tag-input" placeholder="Type and press Enter"
                                       onkeypress="addTag(event, '${taskId}')">
                                <div class="tag-container" id="tags_${taskId}"></div>
                                <input type="hidden" name="tasks[${taskCounter}][tags]" id="tags_input_${taskId}">
                            </div>
                        </div>
                    </div>
                `;

                $('#tasksContainer').append(taskForm);
                updateTaskCounter();
            }

            function removeTask(taskId) {
                $(`[data-task-id="${taskId}"]`).remove();
                taskCounter = $('#tasksContainer .repeater-item').length;
                updateTaskCounter();
            }

            function updateTaskCounter() {
                $('#taskCounter').text(taskCounter);
                $('#submitAllBtn').prop('disabled', taskCounter === 0);
            }

            function submitAllTasks() {
                if (taskCounter === 0) {
                    showAlert('Please add at least one task before submitting.', 'warning');
                    return;
                }

                showLoading(true);
                const formData = $('#taskForm').serializeArray();
                const tasks = [];

                // Group form data by task
                const taskData = {};
                formData.forEach(field => {
                    const match = field.name.match(/tasks\[(\d+)\]\[(\w+)\]/);
                    if (match) {
                        const taskIndex = match[1];
                        const fieldName = match[2];

                        if (!taskData[taskIndex]) {
                            taskData[taskIndex] = {};
                        }

                        if (fieldName === 'tags') {
                            taskData[taskIndex][fieldName] = field.value ? JSON.parse(field.value) : [];
                        } else {
                            taskData[taskIndex][fieldName] = field.value;
                        }
                    }
                });

                // Convert to array
                Object.values(taskData).forEach(task => {
                    if (task.title && task.title.trim()) {
                        // Ensure tags is always an array
                        if (!task.tags) {
                            task.tags = [];
                        }
                        // Ensure due_date is properly formatted or null
                        if (task.due_date && task.due_date.trim() === '') {
                            task.due_date = null;
                        }
                        // Set defaults for missing fields
                        task.priority = task.priority || 'medium';
                        task.status = task.status || 'pending';

                        tasks.push(task);
                    }
                });

                if (tasks.length === 0) {
                    showLoading(false);
                    showAlert('Please fill in at least one complete task.', 'warning');
                    return;
                }

                // Submit via AJAX
                $.ajax({
                    url: '/api/tasks/bulk',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ tasks: tasks }),
                    success: function(response) {
                        showLoading(false);
                        showAlert(`Successfully created ${response.count} tasks in ${response.execution_time_ms}ms!`, 'success');

                        // Show performance info
                        showPerformanceInfo(response);

                        // Clear form and reset
                        $('#tasksContainer').empty();
                        taskCounter = 0;
                        updateTaskCounter();
                        addTaskForm();

                        // Reload statistics and recent tasks
                        loadStatistics();
                        loadRecentTasks();
                    },
                    error: function(xhr) {
                        showLoading(false);
                        const response = xhr.responseJSON;
                        if (response && response.errors) {
                            let errorMsg = 'Validation errors:\n';
                            Object.keys(response.errors).forEach(key => {
                                errorMsg += `- ${response.errors[key].join(', ')}\n`;
                            });
                            showAlert(errorMsg, 'danger');
                        } else {
                            showAlert(response?.message || 'Failed to create tasks. Please try again.', 'danger');
                        }
                    }
                });
            }

            function loadStatistics() {
                $.get('/api/tasks-stats', function(response) {
                    if (response.status === 'success') {
                        displayStatistics(response.data);
                    }
                });
            }

            function displayStatistics(stats) {
                const statsHtml = `
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number text-primary">${stats.total_tasks}</div>
                            <div class="fw-bold">Total Tasks</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number text-warning">${stats.by_status.pending}</div>
                            <div class="fw-bold">Pending</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number text-info">${stats.by_status.in_progress}</div>
                            <div class="fw-bold">In Progress</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number text-success">${stats.by_status.completed}</div>
                            <div class="fw-bold">Completed</div>
                        </div>
                    </div>
                `;
                $('#statsRow').html(statsHtml);
            }

            function loadRecentTasks() {
                $.get('/api/tasks?per_page=10&sort_by=created_at&sort_order=desc', function(response) {
                    if (response.status === 'success') {
                        displayRecentTasks(response.data.data);
                    }
                });
            }

            function displayRecentTasks(tasks) {
                if (tasks.length === 0) {
                    $('#recentTasksList').html('<p class="text-muted text-center py-4">No tasks found. Create your first task above!</p>');
                    return;
                }

                let tasksHtml = '<div class="row">';
                tasks.forEach(task => {
                    const priorityClass = `priority-${task.priority}`;
                    const statusClass = `status-${task.status}`;
                    const dueDate = task.due_date ? new Date(task.due_date).toLocaleDateString() : 'No due date';

                    tasksHtml += `
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0">${task.title}</h6>
                                        <div>
                                            <span class="priority-badge ${priorityClass}">${task.priority}</span>
                                            <span class="status-badge ${statusClass}">${task.status.replace('_', ' ')}</span>
                                        </div>
                                    </div>
                                    <p class="card-text text-muted small">${task.description || 'No description'}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            ${dueDate}
                                        </small>
                                        <small class="text-muted">
                                            Created: ${new Date(task.created_at).toLocaleDateString()}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                tasksHtml += '</div>';
                $('#recentTasksList').html(tasksHtml);
            }

            function showPerformanceInfo(response) {
                const performanceHtml = `
                    <div class="performance-info">
                        <h5><i class="fas fa-tachometer-alt me-2"></i>Performance Information</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Execution Time:</strong> ${response.execution_time_ms}ms
                            </div>
                            <div class="col-md-4">
                                <strong>Tasks Created:</strong> ${response.count}
                            </div>
                            <div class="col-md-4">
                                <strong>Tasks/Second:</strong> ${response.performance_info?.tasks_per_second || 'N/A'}
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Using optimized bulk insert for better performance
                            </small>
                        </div>
                    </div>
                `;
                $('#performanceInfo').html(performanceHtml).show();

                // Hide after 10 seconds
                setTimeout(() => {
                    $('#performanceInfo').fadeOut();
                }, 10000);
            }

            function showLoading(show) {
                if (show) {
                    $('#loadingOverlay').show();
                } else {
                    $('#loadingOverlay').hide();
                }
            }

            function showAlert(message, type) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;

                // Remove existing alerts
                $('.alert').remove();

                // Add new alert to top of container
                $('.container').prepend(alertHtml);

                // Auto-hide after 5 seconds
                setTimeout(() => {
                    $('.alert').fadeOut();
                }, 5000);
            }

            // Global functions for tags
            window.addTag = function(event, taskId) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    const input = event.target;
                    const tag = input.value.trim();

                    if (tag) {
                        const tagContainer = document.getElementById(`tags_${taskId}`);
                        const tagInput = document.getElementById(`tags_input_${taskId}`);

                        // Get existing tags
                        let existingTags = [];
                        try {
                            existingTags = JSON.parse(tagInput.value || '[]');
                        } catch (e) {
                            existingTags = [];
                        }

                        // Add new tag if not exists
                        if (!existingTags.includes(tag)) {
                            existingTags.push(tag);

                            // Update hidden input
                            tagInput.value = JSON.stringify(existingTags);

                            // Update display
                            updateTagDisplay(taskId, existingTags);
                        }

                        input.value = '';
                    }
                }
            };

            window.removeTask = removeTask;

            function updateTagDisplay(taskId, tags) {
                const tagContainer = document.getElementById(`tags_${taskId}`);
                tagContainer.innerHTML = tags.map(tag => `
                    <span class="tag-item">
                        ${tag}
                        <span class="tag-remove" onclick="removeTag('${taskId}', '${tag}')">&times;</span>
                    </span>
                `).join('');
            }

            window.removeTag = function(taskId, tagToRemove) {
                const tagInput = document.getElementById(`tags_input_${taskId}`);
                let tags = JSON.parse(tagInput.value || '[]');
                tags = tags.filter(tag => tag !== tagToRemove);
                tagInput.value = JSON.stringify(tags);
                updateTagDisplay(taskId, tags);
            };
        });
    </script>
</div>
@endsection

@section('scripts')
<script>
    // Tasks-specific JavaScript will be loaded here
    // The main functionality is already included above
</script>
@endsection
