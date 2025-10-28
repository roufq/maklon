<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for authentication
        $this->user = User::factory()->create();
        $this->user->assignRole('Admin'); // Assign admin role for full access
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_can_list_projects()
    {
        Project::factory()->count(3)->create(['created_by' => $this->user->id]);

        $response = $this->get(route('projects.index'));

        $response->assertStatus(200);
        $response->assertViewHas('projects');
        $response->assertViewHas('projects', function ($projects) {
            return $projects->count() === 3;
        });
    }

    #[Test]
    public function it_can_create_a_project()
    {
        $projectData = [
            'name' => 'Test Project',
            'description' => 'This is a test project',
            'status' => 'active',
            'priority' => 'medium',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
            'budget' => 10000.00,
        ];

        $response = $this->post(route('projects.store'), $projectData);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', $projectData);
    }

    #[Test]
    public function it_can_show_a_project()
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        $response = $this->get(route('projects.show', $project));

        $response->assertStatus(200);
        $response->assertViewHas('project', $project);
    }

    #[Test]
    public function it_can_update_a_project()
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        $updatedData = [
            'name' => 'Updated Project Name',
            'description' => 'Updated description',
            'status' => 'completed',
            'priority' => 'high',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(60)->format('Y-m-d'),
            'budget' => 20000.00,
        ];

        $response = $this->put(route('projects.update', $project), $updatedData);

        $response->assertRedirect(route('projects.show', $project));
        $this->assertDatabaseHas('projects', $updatedData);
    }

    #[Test]
    public function it_can_delete_a_project()
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        $response = $this->delete(route('projects.destroy', $project));

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    #[Test]
    public function it_validates_required_fields_when_creating_project()
    {
        $response = $this->post(route('projects.store'), []);

        $response->assertSessionHasErrors(['name', 'status']);
    }

    #[Test]
    public function it_validates_required_fields_when_updating_project()
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);

        $response = $this->put(route('projects.update', $project), []);

        $response->assertSessionHasErrors(['name', 'status']);
    }
}
