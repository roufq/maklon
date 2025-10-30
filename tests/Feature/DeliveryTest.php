<?php

namespace Tests\Feature;

use App\Models\Delivery;
use App\Models\ProductionBatch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DeliveryTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $batch;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for authentication
        $this->user = User::factory()->create();
        $this->user->assignRole('Admin'); // Assign admin role for full access
        $this->actingAs($this->user);

        // Create a batch and customer for deliveries
        $this->batch = ProductionBatch::factory()->create(['qc_status' => 'passed']);
        $this->customer = User::factory()->create();
        $this->customer->assignRole('CS');
    }

    #[Test]
    public function it_can_list_deliveries()
    {
        Delivery::factory()->count(3)->create([
            'production_batch_id' => $this->batch->id,
            'customer_id' => $this->customer->id
        ]);

        $response = $this->get(route('deliveries.index'));

        $response->assertStatus(200);
        $response->assertViewHas('deliveries');
        $response->assertViewHas('deliveries', function ($deliveries) {
            return $deliveries->count() === 3;
        });
    }

    #[Test]
    public function it_can_create_a_delivery()
    {
        $deliveryData = [
            'production_batch_id' => $this->batch->id,
            'customer_id' => $this->customer->id,
            'quantity' => 100,
            'shipping_address' => '123 Test Street, Test City',
            'tracking_number' => 'TRK123456789',
            'notes' => 'Handle with care',
        ];

        $response = $this->post(route('deliveries.store'), $deliveryData);

        $response->assertRedirect(route('deliveries.show', Delivery::latest()->first()));
        $this->assertDatabaseHas('deliveries', $deliveryData);
    }

    #[Test]
    public function it_can_show_a_delivery()
    {
        $delivery = Delivery::factory()->create([
            'production_batch_id' => $this->batch->id,
            'customer_id' => $this->customer->id
        ]);

        $response = $this->get(route('deliveries.show', $delivery));

        $response->assertStatus(200);
        $response->assertViewHas('delivery', $delivery);
    }

    #[Test]
    public function it_can_update_a_delivery()
    {
        $delivery = Delivery::factory()->create([
            'production_batch_id' => $this->batch->id,
            'customer_id' => $this->customer->id
        ]);

        $updatedData = [
            'production_batch_id' => $this->batch->id,
            'customer_id' => $this->customer->id,
            'quantity' => 150,
            'shipping_address' => '456 Updated Street, Updated City',
            'tracking_number' => 'TRK987654321',
            'status' => 'shipped',
            'notes' => 'Updated notes',
        ];

        $response = $this->put(route('deliveries.update', $delivery), $updatedData);

        $response->assertRedirect(route('deliveries.show', $delivery));
        $this->assertDatabaseHas('deliveries', $updatedData);
    }

    #[Test]
    public function it_can_delete_a_delivery()
    {
        $delivery = Delivery::factory()->create([
            'production_batch_id' => $this->batch->id,
            'customer_id' => $this->customer->id
        ]);

        $response = $this->delete(route('deliveries.destroy', $delivery));

        $response->assertRedirect(route('deliveries.index'));
        $this->assertDatabaseMissing('deliveries', ['id' => $delivery->id]);
    }

    #[Test]
    public function it_validates_required_fields_when_creating_delivery()
    {
        $response = $this->post(route('deliveries.store'), []);

        $response->assertSessionHasErrors(['production_batch_id', 'customer_id', 'quantity']);
    }

    #[Test]
    public function it_validates_required_fields_when_updating_delivery()
    {
        $delivery = Delivery::factory()->create([
            'production_batch_id' => $this->batch->id,
            'customer_id' => $this->customer->id
        ]);

        $response = $this->put(route('deliveries.update', $delivery), []);

        $response->assertSessionHasErrors(['production_batch_id', 'customer_id', 'quantity']);
    }

    #[Test]
    public function it_prevents_delivery_if_batch_not_passed_qc()
    {
        $failedBatch = ProductionBatch::factory()->create(['qc_status' => 'failed']);
        $project = \App\Models\Project::factory()->create(['created_by' => $this->user->id]);

        $deliveryData = [
            'project_id' => $project->id,
            'production_batch_id' => $failedBatch->id,
            'customer_id' => $this->customer->id,
            'quantity' => 100,
            'shipping_address' => '123 Test Street, Test City',
        ];

        $response = $this->post(route('deliveries.store'), $deliveryData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('deliveries', ['production_batch_id' => $failedBatch->id]);
    }

    #[Test]
    public function it_can_filter_deliveries_by_status()
    {
        Delivery::factory()->create([
            'status' => 'pending',
            'production_batch_id' => $this->batch->id,
            'customer_id' => $this->customer->id
        ]);

        Delivery::factory()->create([
            'status' => 'shipped',
            'production_batch_id' => $this->batch->id,
            'customer_id' => $this->customer->id
        ]);

        $response = $this->get(route('deliveries.index', ['status' => 'pending']));

        $response->assertStatus(200);
        $response->assertViewHas('deliveries', function ($deliveries) {
            return $deliveries->count() === 1 && $deliveries->first()->status === 'pending';
        });
    }
}
