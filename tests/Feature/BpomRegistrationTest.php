<?php

namespace Tests\Feature;

use App\Models\BpomRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class BpomRegistrationTest extends TestCase
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
    public function it_can_list_bpom_registrations()
    {
        BpomRegistration::factory()->count(3)->create();

        $response = $this->get(route('bpom.index'));

        $response->assertStatus(200);
        $response->assertViewHas('items');
        $response->assertViewHas('items', function ($items) {
            return $items->count() === 3;
        });
    }

    #[Test]
    public function it_can_create_a_bpom_registration()
    {
        $registrationData = [
            'product_name' => 'Test Product',
            'registration_number' => 'BPOM123456789',
            'approval_date' => now()->format('Y-m-d'),
            'expiry_date' => now()->addYear()->format('Y-m-d'),
            'status' => 'active',
        ];

        $response = $this->post(route('bpom.store'), $registrationData);

        $response->assertRedirect(route('bpom.show', BpomRegistration::latest()->first()));
        $this->assertDatabaseHas('bpom_registrations', [
            'product_name' => 'Test Product',
            'registration_number' => 'BPOM123456789',
            'status' => 'active',
        ]);
    }

    #[Test]
    public function it_can_show_a_bpom_registration()
    {
        $registration = BpomRegistration::factory()->create();

        $response = $this->get(route('bpom.show', $registration));

        $response->assertStatus(200);
        $response->assertViewHas('registration', $registration);
    }

    #[Test]
    public function it_can_update_a_bpom_registration()
    {
        $registration = BpomRegistration::factory()->create();

        $updatedData = [
            'product_name' => 'Updated Product Name',
            'registration_number' => 'BPOM987654321',
            'approval_date' => now()->subDays(30)->format('Y-m-d'),
            'expiry_date' => now()->addMonths(6)->format('Y-m-d'),
            'status' => 'expired',
        ];

        $response = $this->put(route('bpom.update', $registration), $updatedData);

        $response->assertRedirect(route('bpom.show', $registration));
        $this->assertDatabaseHas('bpom_registrations', $updatedData);
    }

    #[Test]
    public function it_can_delete_a_bpom_registration()
    {
        $registration = BpomRegistration::factory()->create();

        $response = $this->delete(route('bpom.destroy', $registration));

        $response->assertRedirect(route('bpom.index'));
        $this->assertDatabaseMissing('bpom_registrations', ['id' => $registration->id]);
    }

    #[Test]
    public function it_validates_required_fields_when_creating_bpom_registration()
    {
        $response = $this->post(route('bpom.store'), []);

        $response->assertSessionHasErrors(['product_name', 'registration_number', 'status']);
    }

    #[Test]
    public function it_validates_required_fields_when_updating_bpom_registration()
    {
        $registration = BpomRegistration::factory()->create();

        $response = $this->put(route('bpom.update', $registration), []);

        $response->assertSessionHasErrors(['product_name', 'registration_number', 'status']);
    }

    #[Test]
    public function it_validates_unique_registration_number()
    {
        BpomRegistration::factory()->create(['registration_number' => 'BPOM123456789']);

        $response = $this->post(route('bpom.store'), [
            'product_name' => 'Another Product',
            'registration_number' => 'BPOM123456789', // Duplicate
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors(['registration_number']);
    }

    #[Test]
    public function it_can_filter_registrations_by_status()
    {
        BpomRegistration::factory()->create(['status' => 'active']);
        BpomRegistration::factory()->create(['status' => 'expired']);

        $response = $this->get(route('bpom.index', ['status' => 'active']));

        $response->assertStatus(200);
        $response->assertViewHas('items', function ($items) {
            return $items->count() === 1 && $items->first()->status === 'active';
        });
    }
}
