<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a variety of tasks for testing
        $sampleTasks = [
            [
                'title' => 'Complete project documentation',
                'description' => 'Write comprehensive documentation for the task management system including API docs and user guide',
                'priority' => 'high',
                'status' => 'in_progress',
                'due_date' => now()->addDays(3),
                'tags' => ['documentation', 'urgent', 'high-priority']
            ],
            [
                'title' => 'Implement user authentication',
                'description' => 'Add login/logout functionality with role-based access control',
                'priority' => 'high',
                'status' => 'pending',
                'due_date' => now()->addWeek(),
                'tags' => ['authentication', 'security', 'feature']
            ],
            [
                'title' => 'Optimize database queries',
                'description' => 'Review and optimize slow-running database queries for better performance',
                'priority' => 'medium',
                'status' => 'pending',
                'due_date' => now()->addDays(10),
                'tags' => ['performance', 'database', 'optimization']
            ],
            [
                'title' => 'Setup automated testing',
                'description' => 'Configure PHPUnit tests and set up continuous integration',
                'priority' => 'medium',
                'status' => 'pending',
                'due_date' => now()->addDays(14),
                'tags' => ['testing', 'ci-cd', 'quality']
            ],
            [
                'title' => 'Design UI/UX improvements',
                'description' => 'Create mockups for improved user interface and user experience',
                'priority' => 'low',
                'status' => 'pending',
                'due_date' => now()->addDays(21),
                'tags' => ['design', 'ui', 'ux']
            ],
            [
                'title' => 'Code review for pull request #123',
                'description' => 'Review and provide feedback on the new feature implementation',
                'priority' => 'medium',
                'status' => 'completed',
                'due_date' => now()->subDays(1),
                'tags' => ['review', 'completed', 'development']
            ],
            [
                'title' => 'Fix critical production bug',
                'description' => 'Investigate and fix the memory leak issue affecting production servers',
                'priority' => 'high',
                'status' => 'completed',
                'due_date' => now()->subDays(2),
                'tags' => ['bug', 'critical', 'production', 'completed']
            ],
            [
                'title' => 'Update third-party dependencies',
                'description' => 'Update all composer and npm dependencies to their latest stable versions',
                'priority' => 'low',
                'status' => 'pending',
                'due_date' => now()->addDays(30),
                'tags' => ['maintenance', 'dependencies', 'security']
            ],
            [
                'title' => 'Implement caching strategy',
                'description' => 'Design and implement Redis-based caching for improved performance',
                'priority' => 'medium',
                'status' => 'in_progress',
                'due_date' => now()->addDays(7),
                'tags' => ['performance', 'caching', 'redis']
            ],
            [
                'title' => 'Create backup and recovery plan',
                'description' => 'Develop comprehensive backup strategy and disaster recovery procedures',
                'priority' => 'high',
                'status' => 'pending',
                'due_date' => now()->addDays(5),
                'tags' => ['backup', 'recovery', 'infrastructure']
            ]
        ];

        // Insert sample tasks
        foreach ($sampleTasks as $taskData) {
            Task::create($taskData);
        }

        // Generate additional random tasks for performance testing
        Task::factory(50)->create();
    }
}
