<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'user@example.com',
            'role' => 'user',
        ]);

        $tasks = [
            ['title' => 'Set up project infrastructure', 'description' => 'Configure CI/CD pipeline, Docker, and deployment scripts for the new project.', 'priority' => 'high', 'status' => 'completed'],
            ['title' => 'Design database schema', 'description' => 'Create ERD and finalize all table relationships for the task management system.', 'priority' => 'high', 'status' => 'completed'],
            ['title' => 'Implement user authentication', 'description' => 'Set up Laravel Breeze with role-based access control for admin and regular users.', 'priority' => 'high', 'status' => 'in_progress'],
            ['title' => 'Build task CRUD with repository pattern', 'description' => 'Implement full CRUD operations using the repository pattern and service layer.', 'priority' => 'high', 'status' => 'in_progress'],
            ['title' => 'Integrate AI summary generation', 'description' => 'Connect OpenAI API to auto-generate task summaries and suggest priority levels.', 'priority' => 'medium', 'status' => 'pending'],
            ['title' => 'Create dashboard with analytics', 'description' => 'Build a visual dashboard showing task statistics and completion charts.', 'priority' => 'medium', 'status' => 'pending'],
            ['title' => 'Write feature tests', 'description' => 'Cover all critical flows including task creation, AI integration, and authorization.', 'priority' => 'medium', 'status' => 'pending'],
            ['title' => 'Documentation and README', 'description' => 'Write comprehensive README covering architecture, AI prompts, and setup instructions.', 'priority' => 'low', 'status' => 'pending'],
        ];

        foreach ($tasks as $i => $data) {
            Task::create([
                ...$data,
                'created_by' => $admin->id,
                'assigned_to' => $i % 2 === 0 ? $user->id : $admin->id,
                'due_date' => now()->addDays(rand(3, 30))->format('Y-m-d'),
                'ai_summary' => 'AI has analyzed this task. It involves ' . strtolower($data['title']) . ' which is critical for the project timeline.',
                'ai_priority' => $data['priority'],
            ]);
        }
    }
}
