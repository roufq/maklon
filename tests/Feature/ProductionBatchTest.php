<?php

namespace Tests\Feature;

use App\Models\ProductionBatch;
use App\Models\Project;
use App\Models\User;
use App\Models\BpomRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ProductionBatchTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $project;
    protected $bpom;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for authentication
        $this->user = User::factory()->create();
        $this->user->assignRole('Admin'); // Assign admin role for full access
        $this->actingAs($this->user);

        // Create a project and BPOM registration for batches
        $this->project = Project::factory()->create(['created_by' => $this->user->id]);
        $this->bpom = BpomRegistration::factory()->create();
    }

    #[Test]
    public function it_can_list_production_batches()
    {
        ProductionBatch::factory()->count(3)->create([
            'project_id' => $this->project->id,
            'bpom_registration_id' => $this->bpom->id
        ]);

        $response = $this->get(route('batches.index'));

        $response->assertStatus(200);
        $response->assertViewHas('batches');
        $response->assertViewHas('batches', function ($batches) {
            return $batches->count() === 3;
        });
    }

    #[Test]
    public function it_can_create_a_production_batch()
    {
        $batchData = [
            'project_id' => $this->project->id,
            'bpom_registration_id' => $this->bpom->id,
            'batch_number' => 'TEST-001',
            'quantity_produced' => 1000,
            'expiry_date' => now()->addMonths(12)->format('Y-m-d'),
            'qc_status' => 'pending',
        ];

        $response = $this->post(route('batches.store'), $batchData);

        $response->assertRedirect(route('batches.index'));
        $this->assertDatabaseHas('production_batches', $batchData);
    }

    #[Test]
    public function it_can_show_a_production_batch()
    {
        $batch = ProductionBatch::factory()->create([
            'project_id' => $this->project->id,
            'bpom_registration_id' => $this->bpom->id
        ]);

        $response = $this->get(route('batches.show', $batch));

        $response->assertStatus(200);
        $response->assertViewHas('batch', $batch);
    }

    #[Test]
    public function it_can_update_a_production_batch()
    {
        $batch = ProductionBatch::factory()->create([
            'project_id' => $this->project->id,
            'bpom_registration_id' => $this->bpom->id
        ]);

        $updatedData = [
            'project_id' => $this->project->id,
            'bpom_registration_id' => $this->bpom->id,
            'batch_number' => 'UPDATED-001',
            'quantity_produced' => 2000,
            'expiry_date' => now()->addMonths(18)->format('Y-m-d'),
            'qc_status' => 'passed',
        ];

        $response = $this->put(route('batches.update', $batch), $updatedData);

        $response->assertRedirect(route('batches.show', $batch));
        $this->assertDatabaseHas('production_batches', $updatedData);
    }

    #[Test]
    public function it_can_delete_a_production_batch()
    {
        $batch = ProductionBatch::factory()->create([
            'project_id' => $this->project->id,
            'bpom_registration_id' => $this->bpom->id
        ]);

        $response = $this->delete(route('batches.destroy', $batch));

        $response->assertRedirect(route('batches.index'));
        $this->assertDatabaseMissing('production_batches', ['id' => $batch->id]);
    }

    #[Test]
    public function it_validates_required_fields_when_creating_batch()
    {
        $response = $this->post(route('batches.store'), []);

        $response->assertSessionHasErrors(['project_id', 'bpom_registration_id', 'batch_number', 'quantity_produced', 'qc_status']);
    }

    #[Test]
    public function it_validates_required_fields_when_updating_batch()
    {
        $batch = ProductionBatch::factory()->create([
            'project_id' => $this->project->id,
            'bpom_registration_id' => $this->bpom->id
        ]);

        $response = $this->put(route('batches.update', $batch), []);

        $response->assertSessionHasErrors(['project_id', 'bpom_registration_id', 'batch_number', 'quantity_produced', 'qc_status']);
    }

    #[Test]
    public function it_can_filter_batches_by_qc_status()
    {
        ProductionBatch::factory()->create([
            'qc_status' => 'pending',
            'project_id' => $this->project->id,
            'bpom_registration_id' => $this->bpom->id
        ]);

        ProductionBatch::factory()->create([
            'qc_status' => 'passed',
            'project_id' => $this->project->id,
            'bpom_registration_id' => $this->bpom->id
        ]);

        $response = $this->get(route('batches.index', ['qc_status' => 'pending']));

        $response->assertStatus(200);
        $response->assertViewHas('batches', function ($batches) {
            return $batches->count() === 1 && $batches->first()->qc_status === 'pending';
        });
    }
}
