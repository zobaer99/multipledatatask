<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Task;

echo "Performance Testing Task Management System\n";
echo "==========================================\n\n";

// Test 1: Single task creation performance
echo "Test 1: Single Task Creation Performance\n";
$start = microtime(true);
$task = Task::create([
    'title' => 'Performance Test Single Task',
    'description' => 'Testing single task creation performance',
    'priority' => 'medium',
    'status' => 'pending'
]);
$singleTime = round((microtime(true) - $start) * 1000, 2);
echo "Created single task in {$singleTime}ms\n\n";

// Test 2: Bulk insert 10 tasks
echo "Test 2: Bulk Insert 10 Tasks\n";
$start = microtime(true);
$tasks = [];
for ($i = 1; $i <= 10; $i++) {
    $tasks[] = [
        'title' => "Bulk Test Task {$i}",
        'description' => 'Testing bulk insert performance',
        'priority' => 'medium',
        'status' => 'pending',
        'created_at' => now(),
        'updated_at' => now()
    ];
}
Task::insert($tasks);
$bulkTime10 = round((microtime(true) - $start) * 1000, 2);
echo "Bulk inserted 10 tasks in {$bulkTime10}ms\n\n";

// Test 3: Bulk insert 50 tasks
echo "Test 3: Bulk Insert 50 Tasks\n";
$start = microtime(true);
$tasks = [];
for ($i = 1; $i <= 50; $i++) {
    $tasks[] = [
        'title' => "Bulk Test Task Large {$i}",
        'description' => 'Testing bulk insert performance with larger dataset',
        'priority' => ['low', 'medium', 'high'][rand(0, 2)],
        'status' => ['pending', 'in_progress'][rand(0, 1)],
        'created_at' => now(),
        'updated_at' => now()
    ];
}
Task::insert($tasks);
$bulkTime50 = round((microtime(true) - $start) * 1000, 2);
echo "Bulk inserted 50 tasks in {$bulkTime50}ms\n\n";

// Test 4: Query performance
echo "Test 4: Query Performance\n";
$start = microtime(true);
$count = Task::count();
$queryTime = round((microtime(true) - $start) * 1000, 2);
echo "Counted {$count} tasks in {$queryTime}ms\n\n";

// Test 5: Filtered query performance
echo "Test 5: Filtered Query Performance\n";
$start = microtime(true);
$pendingTasks = Task::where('status', 'pending')->where('priority', 'high')->count();
$filteredQueryTime = round((microtime(true) - $start) * 1000, 2);
echo "Found {$pendingTasks} high priority pending tasks in {$filteredQueryTime}ms\n\n";

// Performance Summary
echo "Performance Summary\n";
echo "===================\n";
echo "Single Task Creation: {$singleTime}ms\n";
echo "Bulk Insert (10 tasks): {$bulkTime10}ms (" . round(10 / ($bulkTime10 / 1000), 2) . " tasks/second)\n";
echo "Bulk Insert (50 tasks): {$bulkTime50}ms (" . round(50 / ($bulkTime50 / 1000), 2) . " tasks/second)\n";
echo "Simple Query: {$queryTime}ms\n";
echo "Filtered Query: {$filteredQueryTime}ms\n\n";

// Efficiency comparison
$efficiency10 = $bulkTime10 / 10;
$efficiency50 = $bulkTime50 / 50;
echo "Efficiency Metrics\n";
echo "==================\n";
echo "Time per task (bulk 10): " . round($efficiency10, 2) . "ms\n";
echo "Time per task (bulk 50): " . round($efficiency50, 2) . "ms\n";
echo "Efficiency improvement: " . round((($efficiency10 - $efficiency50) / $efficiency10) * 100, 1) . "%\n\n";

echo "Performance test completed successfully!\n";
