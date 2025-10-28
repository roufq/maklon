<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $project;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for authentication
        $this->user = User::factory()->create();
        $this->user->assignRole('Admin'); // Assign admin role for full access
        $this->actingAs($this->user);

        // Create a project for tasks
        $this->project = Project::factory()->create(['created_by' => $this->user->id]);
    }

    #[Test]
    public function it_can_list_tasks()
    {
        Task::factory()->count(3)->create([
            'project_id' => $this->project->id,
            'created_by' => $this->user->id
        ]);

        $response = $this->get(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertViewHas('tasks');
        $response->assertViewHas('tasks', function ($tasks) {
            return $tasks->count() === 3;
        });
    }

    #[Test]
    public function it_can_create_a_task()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'status' => 'todo',
            'priority' => 'medium',
            'project_id' => $this->project->id,
            'assigned_to' => $this->user->id,
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'estimated_hours' => 8,
        ];

        $response = $this->post(route('tasks.store'), $taskData);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'status' => 'todo',
            'priority' => 'medium',
            'project_id' => $this->project->id,
            'assigned_to' => $this->user->id,
            'estimated_hours' => 8,
        ]);
    }

    #[Test]
    public function it_can_show_a_task()
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'created_by' => $this->user->id
        ]);

        $response = $this->get(route('tasks.show', $task));

        $response->assertStatus(200);
        $response->assertViewHas('task', $task);
    }

    #[Test]
    public function it_can_update_a_task()
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'created_by' => $this->user->id
        ]);

        $updatedData = [
            'title' => 'Updated Task Title',
            'description' => 'Updated description',
            'status' => 'in_progress',
            'priority' => 'high',
            'project_id' => $this->project->id,
            'assigned_to' => $this->user->id,
            'due_date' => now()->addDays(14)->format('Y-m-d'),
            'estimated_hours' => 16,
        ];

        $response = $this->put(route('tasks.update', $task), $updatedData);

        $response->assertRedirect(route('tasks.show', $task));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Updated Task Title',
            'description' => 'Updated description',
            'status' => 'in_progress',
            'priority' => 'high',
            'project_id' => $this->project->id,
            'assigned_to' => $this->user->id,
            'estimated_hours' => 16,
        ]);
    }

    #[Test]
    public function it_can_delete_a_task()
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'created_by' => $this->user->id
        ]);

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    #[Test]
    public function it_validates_required_fields_when_creating_task()
    {
        $response = $this->post(route('tasks.store'), []);

        $response->assertSessionHasErrors(['title', 'status', 'project_id']);
    }

    #[Test]
    public function it_validates_required_fields_when_updating_task()
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'created_by' => $this->user->id
        ]);

        $response = $this->put(route('tasks.update', $task), []);

        $response->assertSessionHasErrors(['title', 'status', 'project_id']);
    }

    #[Test]
    public function it_can_filter_tasks_by_status()
    {
        Task::factory()->create([
            'status' => 'todo',
            'project_id' => $this->project->id,
            'created_by' => $this->user->id
        ]);

        Task::factory()->create([
            'status' => 'completed',
            'project_id' => $this->project->id,
            'created_by' => $this->user->id
        ]);

        $response = $this->get(route('tasks.index', ['status' => 'todo']));

        $response->assertStatus(200);
        $response->assertViewHas('tasks', function ($tasks) {
            return $tasks->count() === 1 && $tasks->first()->status === 'todo';
        });
    }
}
