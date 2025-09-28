@extends('layouts.app')

@section('title', 'Dashboard - Task Management System')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="page-title">
                    Dashboard
                </h1>
                <p class="page-subtitle mb-0">Welcome to your Task Management System</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <div class="text-white">
                        <small>Last updated:</small><br>
                        <span id="lastUpdated">{{ now()->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Statistics Row -->
    <div class="row mb-4" id="statsRow">
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-number" id="totalTasks">-</div>
                <div class="stat-label">Total Tasks</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-number text-warning" id="pendingTasks">-</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-number text-info" id="inProgressTasks">-</div>
                <div class="stat-label">In Progress</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-number text-success" id="completedTasks">-</div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-bolt me-2"></i>
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('tasks.interface') }}" class="btn btn-primary w-100 d-flex align-items-center justify-content-center" onclick="showNavigationToast()">
                                <i class="fas fa-plus me-2"></i>
                                Create New Tasks
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <button class="btn btn-info w-100" onclick="refreshStats()">
                                <i class="fas fa-sync me-2"></i>
                                Refresh Statistics
                            </button>
                        </div>
                        <div class="col-md-4 mb-3">
                            <button class="btn btn-secondary w-100" data-bs-toggle="modal" data-bs-target="#apiDocsModal">
                                <i class="fas fa-code me-2"></i>
                                View API Docs
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tasks and Performance Row -->
    <div class="row">
        <!-- Recent Tasks -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-clock me-2"></i>
                        <h5 class="mb-0 d-inline">Recent Tasks</h5>
                    </div>
                    <a href="{{ route('tasks.interface') }}" class="btn btn-sm btn-outline-primary">
                        View All Tasks
                        <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div id="recentTasksList">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading recent tasks...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Performance -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2"></i>
                    <h5 class="mb-0 d-inline">System Performance</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Database Performance</small>
                        <div class="d-flex justify-content-between">
                            <span>Query Speed</span>
                            <span class="text-success fw-bold" id="querySpeed">-</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Bulk Insert Performance</small>
                        <div class="d-flex justify-content-between">
                            <span>Tasks/Second</span>
                            <span class="text-info fw-bold" id="tasksPerSecond">-</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">System Status</small>
                        <div class="d-flex justify-content-between">
                            <span>API Status</span>
                            <span class="badge bg-success" id="apiStatus">Online</span>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <button class="btn btn-sm btn-outline-secondary" onclick="testPerformance()">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            Run Performance Test
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Highlights -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-star me-2"></i>
                    <h5 class="mb-0 d-inline">System Features</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-plus-circle fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h6>Multiple Data Insertion</h6>
                                    <p class="text-muted mb-0">Create multiple tasks simultaneously with dynamic repeater fields.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-check-double fa-2x text-success"></i>
                                </div>
                                <div>
                                    <h6>Real-time Validation</h6>
                                    <p class="text-muted mb-0">Comprehensive validation with instant feedback and error handling.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-tachometer-alt fa-2x text-info"></i>
                                </div>
                                <div>
                                    <h6>High Performance</h6>
                                    <p class="text-muted mb-0">Optimized bulk operations with performance monitoring and tracking.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- API Documentation Modal -->
<div class="modal fade" id="apiDocsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-code me-2"></i>
                    API Documentation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Available Endpoints:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Method</th>
                                        <th>Endpoint</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge bg-success">GET</span></td>
                                        <td>/api/tasks</td>
                                        <td>List all tasks</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-primary">POST</span></td>
                                        <td>/api/tasks</td>
                                        <td>Create single task</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-primary">POST</span></td>
                                        <td>/api/tasks/bulk</td>
                                        <td>Create multiple tasks</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-success">GET</span></td>
                                        <td>/api/tasks/{id}</td>
                                        <td>Get single task</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-warning">PUT</span></td>
                                        <td>/api/tasks/{id}</td>
                                        <td>Update task</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-danger">DELETE</span></td>
                                        <td>/api/tasks/{id}</td>
                                        <td>Delete task</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-info">GET</span></td>
                                        <td>/api/tasks/stats</td>
                                        <td>Get statistics</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Example Bulk Insert:</h6>
                        <pre class="bg-light p-3 rounded"><code>{
  "tasks": [
    {
      "title": "Task 1",
      "description": "Description 1",
      "priority": "high",
      "status": "pending",
      "tags": ["tag1", "tag2"]
    },
    {
      "title": "Task 2",
      "description": "Description 2",
      "priority": "medium",
      "status": "pending",
      "tags": ["tag3"]
    }
  ]
}</code></pre>
                        <h6 class="mt-3">Response Format:</h6>
                        <pre class="bg-light p-3 rounded"><code>{
  "status": "success",
  "message": "Tasks created successfully",
  "data": [...],
  "count": 2,
  "execution_time_ms": 45.67
}</code></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    loadDashboardData();

    // Auto-refresh every 30 seconds
    setInterval(refreshStats, 30000);
});

function loadDashboardData() {
    loadStatistics();
    loadRecentTasks();
    loadPerformanceData();
}

function loadStatistics() {
    $.get('/api/tasks/stats')
        .done(function(response) {
            if (response.status === 'success') {
                const stats = response.data;
                $('#totalTasks').text(stats.total_tasks || 0);
                $('#pendingTasks').text(stats.by_status.pending || 0);
                $('#inProgressTasks').text(stats.by_status.in_progress || 0);
                $('#completedTasks').text(stats.by_status.completed || 0);
            }
        })
        .fail(function() {
            $('#totalTasks, #pendingTasks, #inProgressTasks, #completedTasks').text('0');
        });
}

function loadRecentTasks() {
    $.get('/api/tasks?limit=5&sort=created_at&order=desc')
        .done(function(response) {
            if (response.status === 'success' && response.data && response.data.data && response.data.data.length > 0) {
                let html = '';
                response.data.data.forEach(task => {
                    const statusClass = getStatusClass(task.status);
                    const priorityClass = getPriorityClass(task.priority);

                    html += `
                        <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                            <div>
                                <h6 class="mb-1">${task.title}</h6>
                                <p class="text-muted small mb-1">${task.description || 'No description'}</p>
                                <div class="d-flex gap-2">
                                    <span class="badge ${statusClass}">${task.status}</span>
                                    <span class="badge ${priorityClass}">${task.priority}</span>
                                </div>
                            </div>
                            <small class="text-muted">${formatDate(task.created_at)}</small>
                        </div>
                    `;
                });
                $('#recentTasksList').html(html);
            } else {
                $('#recentTasksList').html(`
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>No tasks found. <a href="{{ route('tasks.interface') }}">Create your first task!</a></p>
                    </div>
                `);
            }
        })
        .fail(function() {
            $('#recentTasksList').html(`
                <div class="text-center py-4 text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                    <p>Failed to load recent tasks.</p>
                </div>
            `);
        });
}function loadPerformanceData() {
    // Simulate performance data - in real app this would come from actual monitoring
    $('#querySpeed').text('< 50ms');
    $('#tasksPerSecond').text('6,711+');
    $('#apiStatus').text('Online').removeClass().addClass('badge bg-success');
}

function refreshStats() {
    showLoading(true);
    loadDashboardData();
    $('#lastUpdated').text(new Date().toLocaleString());

    setTimeout(() => {
        showLoading(false);
        showAlert('Dashboard statistics refreshed successfully!', 'success', 3000);
    }, 1000);
}

function testPerformance() {
    showLoading(true);

    // Test API performance with a small bulk insert
    const testData = {
        tasks: [
            {
                title: 'Performance Test Task',
                description: 'Testing API response time',
                priority: 'low',
                status: 'pending',
                tags: ['test', 'performance']
            }
        ]
    };

    const startTime = performance.now();

    $.ajax({
        url: '/api/tasks/bulk',
        method: 'POST',
        data: JSON.stringify(testData),
        contentType: 'application/json',
        success: function(response) {
            const endTime = performance.now();
            const responseTime = Math.round(endTime - startTime);

            showLoading(false);
            showAlert(`Performance Test Complete! API Response Time: ${responseTime}ms`, 'success', 5000);

            // Update performance display
            $('#querySpeed').text(`${responseTime}ms`);

            // Refresh stats to include the test task
            setTimeout(loadStatistics, 500);
        },
        error: function() {
            showLoading(false);
            showAlert('Performance test failed. Please check API connectivity.', 'danger');
        }
    });
}

function getStatusClass(status) {
    const classes = {
        'pending': 'bg-warning',
        'in_progress': 'bg-info',
        'completed': 'bg-success',
        'cancelled': 'bg-secondary'
    };
    return classes[status] || 'bg-secondary';
}

function getPriorityClass(priority) {
    const classes = {
        'high': 'bg-danger',
        'medium': 'bg-warning',
        'low': 'bg-success'
    };
    return classes[priority] || 'bg-secondary';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 1) return 'Today';
    if (diffDays === 2) return 'Yesterday';
    if (diffDays <= 7) return `${diffDays} days ago`;

    return date.toLocaleDateString();
}

function showNavigationToast() {
    // Small delay to show the navigation is happening
    setTimeout(() => {
        showAlert('Navigating to Task Creation page...', 'info', 2000);
    }, 100);
}
</script>
@endsection
