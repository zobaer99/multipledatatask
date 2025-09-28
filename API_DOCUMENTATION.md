# Task Management API Documentation

## Overview
This is a comprehensive Task Management System API that supports multiple data insertion with validation, featuring dynamic repeater fields and optimized performance.

## Base URL
```
http://localhost:8000/api
```

## Authentication
Currently, no authentication is required for this API.

## Endpoints

### 1. Get All Tasks
- **URL**: `/tasks`
- **Method**: `GET`
- **Description**: Retrieve a paginated list of tasks with optional filtering
- **Query Parameters**:
  - `status` (string, optional): Filter by status (pending, in_progress, completed, cancelled)
  - `priority` (string, optional): Filter by priority (low, medium, high)
  - `due_soon` (boolean, optional): Show tasks due soon
  - `overdue` (boolean, optional): Show overdue tasks
  - `sort_by` (string, optional): Sort field (created_at, due_date, priority, title)
  - `sort_order` (string, optional): Sort order (asc, desc)
  - `per_page` (integer, optional): Items per page (1-100, default: 15)

**Example Request**:
```bash
GET /api/tasks?status=pending&priority=high&per_page=20
```

**Example Response**:
```json
{
  "status": "success",
  "data": {
    "data": [
      {
        "id": 1,
        "title": "Complete project documentation",
        "description": "Write comprehensive documentation",
        "priority": "high",
        "status": "pending",
        "due_date": "2024-01-15",
        "tags": ["documentation", "urgent"],
        "created_at": "2024-01-01T00:00:00Z",
        "updated_at": "2024-01-01T00:00:00Z"
      }
    ],
    "current_page": 1,
    "last_page": 5,
    "total": 100
  },
  "execution_time_ms": 45.23,
  "filters_applied": {
    "status": "pending",
    "priority": "high"
  },
  "total_tasks": 100
}
```

### 2. Create Single Task
- **URL**: `/tasks`
- **Method**: `POST`
- **Description**: Create a new task with validation

**Request Body**:
```json
{
  "title": "Complete project documentation",
  "description": "Write comprehensive documentation for the project",
  "priority": "high",
  "status": "pending",
  "due_date": "2024-01-15",
  "tags": ["documentation", "urgent"]
}
```

**Response**:
```json
{
  "status": "success",
  "message": "Task created successfully",
  "data": {
    "id": 1,
    "title": "Complete project documentation",
    "description": "Write comprehensive documentation for the project",
    "priority": "high",
    "status": "pending",
    "due_date": "2024-01-15",
    "tags": ["documentation", "urgent"],
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-01T00:00:00Z"
  },
  "execution_time_ms": 23.45
}
```

### 3. Bulk Create Tasks (Multiple Data Insertion)
- **URL**: `/tasks/bulk`
- **Method**: `POST`
- **Description**: Create multiple tasks at once using optimized bulk insert
- **Limits**: 1-100 tasks per request

**Request Body**:
```json
{
  "tasks": [
    {
      "title": "Complete project documentation",
      "description": "Write comprehensive docs",
      "priority": "high",
      "status": "pending",
      "due_date": "2024-01-15",
      "tags": ["documentation", "urgent"]
    },
    {
      "title": "Review pull requests",
      "description": "Review pending PRs",
      "priority": "medium",
      "tags": ["review", "development"]
    },
    {
      "title": "Update dependencies",
      "description": "Update all npm and composer packages",
      "priority": "low",
      "status": "pending"
    }
  ]
}
```

**Response**:
```json
{
  "status": "success",
  "message": "Tasks created successfully",
  "data": [
    {
      "id": 1,
      "title": "Complete project documentation",
      "description": "Write comprehensive docs",
      "priority": "high",
      "status": "pending",
      "due_date": "2024-01-15",
      "tags": ["documentation", "urgent"],
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z"
    },
    {
      "id": 2,
      "title": "Review pull requests",
      "description": "Review pending PRs",
      "priority": "medium",
      "status": "pending",
      "tags": ["review", "development"],
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z"
    }
  ],
  "count": 3,
  "execution_time_ms": 67.89,
  "performance_info": {
    "bulk_insert_used": true,
    "tasks_per_second": 44.12
  }
}
```

### 4. Get Single Task
- **URL**: `/tasks/{id}`
- **Method**: `GET`
- **Description**: Retrieve a specific task by ID

**Example Request**:
```bash
GET /api/tasks/1
```

**Response**:
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "title": "Complete project documentation",
    "description": "Write comprehensive documentation",
    "priority": "high",
    "status": "pending",
    "due_date": "2024-01-15",
    "tags": ["documentation", "urgent"],
    "created_at": "2024-01-01T00:00:00Z",
    "updated_at": "2024-01-01T00:00:00Z"
  }
}
```

### 5. Update Task
- **URL**: `/tasks/{id}`
- **Method**: `PUT`
- **Description**: Update an existing task

**Request Body** (same as create task):
```json
{
  "title": "Updated task title",
  "description": "Updated description",
  "priority": "medium",
  "status": "in_progress",
  "due_date": "2024-02-15",
  "tags": ["updated", "in-progress"]
}
```

### 6. Delete Task
- **URL**: `/tasks/{id}`
- **Method**: `DELETE`
- **Description**: Delete a task

**Response**:
```json
{
  "status": "success",
  "message": "Task 'Complete project documentation' deleted successfully"
}
```

### 7. Get Task Statistics
- **URL**: `/tasks-stats`
- **Method**: `GET`
- **Description**: Get comprehensive task statistics

**Response**:
```json
{
  "status": "success",
  "data": {
    "total_tasks": 150,
    "by_status": {
      "pending": 45,
      "in_progress": 20,
      "completed": 80,
      "cancelled": 5
    },
    "by_priority": {
      "low": 30,
      "medium": 85,
      "high": 35
    },
    "overdue_tasks": 12,
    "due_soon": 8
  }
}
```

## Validation Rules

### Task Fields
- **title**: Required, string, 3-255 characters
- **description**: Optional, string, max 1000 characters
- **priority**: Optional, enum (low, medium, high), default: medium
- **status**: Optional, enum (pending, in_progress, completed, cancelled), default: pending
- **due_date**: Optional, date, must be today or future
- **tags**: Optional, array of strings, max 10 tags, each 2-50 characters

### Bulk Insert Rules
- **tasks**: Required, array, 1-100 items
- Each task follows the same validation rules as single task creation

## Error Responses

### Validation Error (422)
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "title": ["The title field is required."],
    "due_date": ["The due date must be a date after or equal to today."]
  },
  "validation_summary": {
    "total_errors": 2,
    "failed_fields": ["title", "due_date"]
  }
}
```

### Bulk Validation Error (422)
```json
{
  "status": "error",
  "message": "Bulk validation failed",
  "errors": {
    "tasks.0.title": ["The title field is required."],
    "tasks.1.priority": ["The selected priority is invalid."]
  },
  "task_errors": {
    "0": {
      "title": ["The title field is required."]
    },
    "1": {
      "priority": ["The selected priority is invalid."]
    }
  },
  "validation_summary": {
    "total_errors": 2,
    "failed_tasks": 2,
    "total_tasks_submitted": 3
  }
}
```

### Server Error (500)
```json
{
  "status": "error",
  "message": "Failed to create tasks",
  "error": "Database connection failed"
}
```

## Performance Features

1. **Bulk Insert Optimization**: Uses Laravel's `insert()` method for optimal database performance
2. **Database Indexing**: Indexes on status, priority, due_date, and created_at columns
3. **Pagination**: Efficient pagination for large datasets
4. **Execution Time Tracking**: All responses include execution time in milliseconds
5. **Query Optimization**: Optimized queries with proper filtering and sorting

## Rate Limits
- Single task creation: No limit
- Bulk task creation: Maximum 100 tasks per request
- API calls: No rate limiting currently implemented

## Examples Using cURL

### Create Single Task
```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -d '{
    "title": "New Task",
    "description": "Task description",
    "priority": "high",
    "status": "pending",
    "due_date": "2024-12-31",
    "tags": ["urgent", "important"]
  }'
```

### Bulk Create Tasks
```bash
curl -X POST http://localhost:8000/api/tasks/bulk \
  -H "Content-Type: application/json" \
  -d '{
    "tasks": [
      {
        "title": "Task 1",
        "description": "Description 1",
        "priority": "high"
      },
      {
        "title": "Task 2",
        "description": "Description 2",
        "priority": "medium"
      }
    ]
  }'
```

### Get Tasks with Filtering
```bash
curl "http://localhost:8000/api/tasks?status=pending&priority=high&per_page=10"
```

## Testing the API

You can test the API using:
1. **Postman**: Import the endpoints and test with the provided examples
2. **cURL**: Use the command examples above
3. **Frontend Interface**: Visit `http://localhost:8000/tasks` for the web interface
4. **Demo Page**: Visit `http://localhost:8000/tasks/demo` for a comprehensive demo

## WebSocket Support
Currently not implemented but planned for real-time updates.

## Future Enhancements
1. Authentication and authorization
2. Real-time notifications
3. File attachments
4. Task dependencies
5. Team collaboration features
6. Advanced reporting and analytics
