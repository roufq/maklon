<?php

namespace Tests\Unit;

use App\Models\Delivery;
use App\Models\InventoryItem;
use App\Models\ProductionBatch;
use App\Models\QualityCheckpoint;
use App\Models\QcResult;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function inventory_item_casts_and_relations_work()
    {
        $supplier = Supplier::factory()->create();
        $item = InventoryItem::factory()->create([
            'supplier_id' => $supplier->id,
            'unit_cost' => 12.5,
        ]);

        $this->assertEquals($supplier->id, $item->supplier->id);
        $this->assertIsString($item->unit_cost);
        $this->assertSame('12.50', $item->unit_cost);
    }

    #[Test]
    public function qc_result_relations_exist()
    {
        $checkpoint = QualityCheckpoint::factory()->create();
        $batch = ProductionBatch::factory()->create();
        $qc = QcResult::factory()->create([
            'quality_checkpoint_id' => $checkpoint->id,
            'production_batch_id' => $batch->id,
        ]);

        $this->assertEquals($checkpoint->id, $qc->checkpoint->id);
        $this->assertEquals($batch->id, $qc->batch->id);
    }

    #[Test]
    public function delivery_tracking_url_accessor_generates_provider_link()
    {
        $customer = User::factory()->create();
        $batch = ProductionBatch::factory()->create();

        $delivery = Delivery::factory()->create([
            'customer_id' => $customer->id,
            'production_batch_id' => $batch->id,
            'shipping_provider' => 'JNE',
            'tracking_number' => 'TEST123',
        ]);

        $this->assertNotNull($delivery->tracking_url);
        $this->assertStringContainsString('awb=TEST123', $delivery->tracking_url);
    }
}

