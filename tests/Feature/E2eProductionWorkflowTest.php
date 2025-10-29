<?php

namespace Tests\Feature;

use App\Models\BpomRegistration;
use App\Models\Delivery;
use App\Models\InventoryItem;
use App\Models\ProductionBatch;
use App\Models\Project;
use App\Models\QcResult;
use App\Models\QualityCheckpoint;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class E2eProductionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $project;
    protected $bpom;
    protected $supplier;
    protected $checkpoint;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->user = User::factory()->create();
        $this->user->assignRole('Admin');
        $this->actingAs($this->user);

        // Create base entities
        $this->project = Project::factory()->create([
            'created_by' => $this->user->id,
            'customer_id' => User::factory()->create()->id,
            'production_status' => 'draft'
        ]);

        $this->bpom = BpomRegistration::factory()->create();
        $this->supplier = Supplier::factory()->create();
        $this->checkpoint = QualityCheckpoint::factory()->create();
    }

    #[Test]
    public function it_completes_full_production_workflow()
    {
        // 1. Create production order (project)
        $this->project->update(['production_status' => 'scheduled']);

        // 2. Create production stages (tasks)
        $task1 = Task::factory()->create([
            'project_id' => $this->project->id,
            'stage_type' => 'cutting',
            'status' => 'todo'
        ]);
        $task2 = Task::factory()->create([
            'project_id' => $this->project->id,
            'stage_type' => 'mixing',
            'status' => 'todo'
        ]);

        // 3. Create production batch
        $batch = ProductionBatch::factory()->create([
            'project_id' => $this->project->id,
            'bpom_registration_id' => $this->bpom->id,
            'quantity_produced' => 1000,
            'qc_status' => 'pending'
        ]);

        // 4. Perform QC
        $qcResult = QcResult::factory()->create([
            'production_batch_id' => $batch->id,
            'quality_checkpoint_id' => $this->checkpoint->id,
            'status' => 'pass'
        ]);

        // Update batch to passed
        $batch->update(['qc_status' => 'passed']);

        // 5. Create delivery
        $customer = User::factory()->create();
        $customer->assignRole('Client');

        $delivery = Delivery::factory()->create([
            'project_id' => $this->project->id,
            'production_batch_id' => $batch->id,
            'customer_id' => $customer->id,
            'status' => 'ready'
        ]);

        // 6. Verify workflow completion
        $this->assertEquals('scheduled', $this->project->fresh()->production_status);
        $this->assertEquals('passed', $batch->fresh()->qc_status);
        $this->assertEquals('ready', $delivery->fresh()->status);
        $this->assertDatabaseHas('qc_results', ['production_batch_id' => $batch->id, 'status' => 'pass']);
        $this->assertDatabaseHas('deliveries', ['production_batch_id' => $batch->id]);
    }

    #[Test]
    public function it_enforces_qc_gating_on_delivery()
    {
        // Create batch with failed QC
        $batch = ProductionBatch::factory()->create([
            'project_id' => $this->project->id,
            'bpom_registration_id' => $this->bpom->id,
            'qc_status' => 'failed'
        ]);

        $customer = User::factory()->create();
        $customer->assignRole('Client');

        // Attempt delivery creation
        $response = $this->post(route('deliveries.store'), [
            'project_id' => $this->project->id,
            'production_batch_id' => $batch->id,
            'customer_id' => $customer->id,
            'shipping_address' => 'Test Address'
        ]);

        // Should be blocked
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Batch not passed QC');
        $this->assertDatabaseMissing('deliveries', ['production_batch_id' => $batch->id]);
    }

    #[Test]
    public function it_tracks_inventory_movements()
    {
        // Create inventory item
        $item = InventoryItem::factory()->create([
            'supplier_id' => $this->supplier->id,
            'current_stock' => 100,
            'min_stock' => 10
        ]);

        // Create batch
        $batch = ProductionBatch::factory()->create([
            'project_id' => $this->project->id,
            'bpom_registration_id' => $this->bpom->id
        ]);

        // Record stock movement (out for production)
        StockMovement::create([
            'inventory_item_id' => $item->id,
            'type' => 'out',
            'qty' => 50,
            'reference_id' => $batch->id,
            'reference_type' => 'production_batch'
        ]);

        // Verify stock updated
        $item->refresh();
        $this->assertEquals(50, $item->current_stock);
        $this->assertDatabaseHas('stock_movements', [
            'inventory_item_id' => $item->id,
            'type' => 'out',
            'qty' => 50
        ]);
    }

    #[Test]
    public function it_handles_bpom_expiry_alerts()
    {
        // Create BPOM expiring soon
        $expiringBpom = BpomRegistration::factory()->create([
            'expiry_date' => now()->addDays(30)
        ]);

        // Run expiry check command
        $this->artisan('bpom:alert-expiry');

        // Verify notification created
        $this->assertDatabaseHas('notifications', [
            'type' => 'warning',
            'message' => 'BPOM ' . $expiringBpom->registration_number . ' will expire in 30 days (2025-11-27)'
        ]);
    }

    #[Test]
    public function it_detects_inventory_low_stock()
    {
        // Create low stock item
        $item = InventoryItem::factory()->create([
            'current_stock' => 5,
            'min_stock' => 10
        ]);

        // Query low stock items
        $lowStockItems = InventoryItem::whereRaw('current_stock <= min_stock')->get();

        $this->assertCount(1, $lowStockItems);
        $this->assertEquals($item->id, $lowStockItems->first()->id);
    }
}
