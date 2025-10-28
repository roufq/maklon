<?php

namespace Tests\Feature;

use App\Models\CalendarEvent;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CalendarEventTest extends TestCase
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

        // Create a project for events
        $this->project = Project::factory()->create(['created_by' => $this->user->id]);
    }

    #[Test]
    public function it_can_list_calendar_events()
    {
        CalendarEvent::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->get(route('calendar.events.index'));

        $response->assertStatus(200);
        $response->assertViewHas('events');
        $response->assertViewHas('events', function ($events) {
            return $events->count() === 3;
        });
    }

    #[Test]
    public function it_can_create_a_calendar_event()
    {
        $eventData = [
            'title' => 'Test Event',
            'description' => 'This is a test calendar event',
            'type' => 'meeting',
            'start_date' => now()->format('Y-m-d H:i:s'),
            'end_date' => now()->addHours(2)->format('Y-m-d H:i:s'),
            'project_id' => $this->project->id,
            'user_id' => $this->user->id,
        ];

        $response = $this->post(route('calendar.events.store'), $eventData);

        $response->assertRedirect(route('calendar.events.index'));
        $this->assertDatabaseHas('calendar_events', [
            'title' => 'Test Event',
            'type' => 'meeting',
            'project_id' => $this->project->id,
        ]);
    }

    #[Test]
    public function it_can_show_a_calendar_event()
    {
        $event = CalendarEvent::factory()->create(['user_id' => $this->user->id]);

        $response = $this->get(route('calendar.events.show', $event));

        $response->assertStatus(200);
        $response->assertViewHas('event', $event);
    }

    #[Test]
    public function it_can_update_a_calendar_event()
    {
        $event = CalendarEvent::factory()->create(['user_id' => $this->user->id]);

        $updatedData = [
            'title' => 'Updated Event Title',
            'description' => 'Updated description',
            'type' => 'milestone',
            'start_date' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_date' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
            'project_id' => $this->project->id,
            'user_id' => $this->user->id,
        ];

        $response = $this->put(route('calendar.events.update', $event), $updatedData);

        $response->assertRedirect(route('calendar.events.index'));
        $this->assertDatabaseHas('calendar_events', [
            'title' => 'Updated Event Title',
            'type' => 'milestone',
        ]);
    }

    #[Test]
    public function it_can_delete_a_calendar_event()
    {
        $event = CalendarEvent::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('calendar.events.destroy', $event));

        $response->assertRedirect(route('calendar.events.index'));
        $this->assertDatabaseMissing('calendar_events', ['id' => $event->id]);
    }

    #[Test]
    public function it_validates_required_fields_when_creating_event()
    {
        $response = $this->post(route('calendar.events.store'), []);

        $response->assertSessionHasErrors(['title', 'type', 'start_date', 'end_date']);
    }

    #[Test]
    public function it_validates_required_fields_when_updating_event()
    {
        $event = CalendarEvent::factory()->create(['user_id' => $this->user->id]);

        $response = $this->put(route('calendar.events.update', $event), []);

        $response->assertSessionHasErrors(['title', 'type', 'start_date', 'end_date']);
    }

    #[Test]
    public function it_can_filter_events_by_type()
    {
        CalendarEvent::factory()->create([
            'type' => 'meeting',
            'user_id' => $this->user->id
        ]);

        CalendarEvent::factory()->create([
            'type' => 'milestone',
            'user_id' => $this->user->id
        ]);

        $response = $this->get(route('calendar.events.index', ['type' => 'meeting']));

        $response->assertStatus(200);
        $response->assertViewHas('events', function ($events) {
            return $events->count() === 1 && $events->first()->type === 'meeting';
        });
    }

    #[Test]
    public function it_can_filter_events_by_project()
    {
        $project2 = Project::factory()->create(['created_by' => $this->user->id]);

        CalendarEvent::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->user->id
        ]);

        CalendarEvent::factory()->create([
            'project_id' => $project2->id,
            'user_id' => $this->user->id
        ]);

        $response = $this->get(route('calendar.events.index', ['project_id' => $this->project->id]));

        $response->assertStatus(200);
        $response->assertViewHas('events', function ($events) {
            return $events->count() === 1 && $events->first()->project_id === $this->project->id;
        });
    }

    #[Test]
    public function it_can_search_events_by_title()
    {
        CalendarEvent::factory()->create([
            'title' => 'Meeting with Client',
            'user_id' => $this->user->id
        ]);

        CalendarEvent::factory()->create([
            'title' => 'Project Review',
            'user_id' => $this->user->id
        ]);

        $response = $this->get(route('calendar.events.index', ['search' => 'Client']));

        $response->assertStatus(200);
        $response->assertViewHas('events', function ($events) {
            return $events->count() === 1 && str_contains($events->first()->title, 'Client');
        });
    }
}
