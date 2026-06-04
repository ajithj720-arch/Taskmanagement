<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user  = User::factory()->create(['role' => 'user']);
    }

    public function test_admin_can_view_task_list(): void
    {
        Task::factory()->count(3)->create(['created_by' => $this->admin->id]);

        $this->actingAs($this->admin)
            ->get(route('tasks.index'))
            ->assertOk()
            ->assertViewIs('tasks.index');
    }

    public function test_authenticated_user_can_create_task(): void
    {
        $this->actingAs($this->user)
            ->post(route('tasks.store'), [
                'title'       => 'Test Task',
                'description' => 'A test task description',
                'priority'    => 'medium',
                'status'      => 'pending',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'title'      => 'Test Task',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_guest_cannot_access_tasks(): void
    {
        $this->get(route('tasks.index'))->assertRedirect(route('login'));
    }

    public function test_user_cannot_edit_task_they_did_not_create(): void
    {
        $task = Task::factory()->create(['created_by' => $this->admin->id]);

        $this->actingAs($this->user)
            ->put(route('tasks.update', $task), [
                'title'    => 'Hacked title',
                'priority' => 'high',
                'status'   => 'completed',
            ])
            ->assertForbidden();
    }

    public function test_admin_can_delete_any_task(): void
    {
        $task = Task::factory()->create(['created_by' => $this->user->id]);

        $this->actingAs($this->admin)
            ->delete(route('tasks.destroy', $task))
            ->assertRedirect(route('tasks.index'));

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_task_status_can_be_updated_by_assignee(): void
    {
        $task = Task::factory()->create([
            'created_by'  => $this->admin->id,
            'assigned_to' => $this->user->id,
            'status'      => 'pending',
        ]);

        $this->actingAs($this->user)
            ->patch(route('tasks.status', $task), ['status' => 'in_progress'])
            ->assertRedirect();

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'in_progress']);
    }

    public function test_task_requires_title_and_priority(): void
    {
        $this->actingAs($this->user)
            ->post(route('tasks.store'), [])
            ->assertSessionHasErrors(['title', 'priority', 'status']);
    }

    public function test_dashboard_shows_stats(): void
    {
        Task::factory()->count(2)->create(['created_by' => $this->admin->id, 'status' => 'completed']);
        Task::factory()->count(3)->create(['created_by' => $this->admin->id, 'status' => 'pending']);

        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('stats');
    }
}
