<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
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
            'title'       => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'priority'    => $this->faker->randomElement(['low', 'medium', 'high']),
            'status'      => $this->faker->randomElement(['pending', 'in_progress', 'completed']),
            'due_date'    => $this->faker->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'created_by'  => User::factory(),
            'assigned_to' => null,
        ];
    }
}
