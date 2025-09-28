<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(2),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed']),
            'due_date' => $this->faker->optional(0.7)->dateTimeBetween('now', '+30 days'),
            'tags' => $this->faker->optional(0.5)->randomElements(
                ['urgent', 'important', 'bug', 'feature', 'improvement', 'documentation'],
                $this->faker->numberBetween(1, 3)
            ),
        ];
    }
}
