<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for authentication
        $this->user = User::factory()->create();
        $this->user->assignRole('Admin'); // Assign admin role for full access
        $this->actingAs($this->user);

        // Create a supplier for inventory items
        $this->supplier = Supplier::factory()->create();
    }

    #[Test]
    public function it_can_list_inventory_items()
    {
        InventoryItem::factory()->count(3)->create([
            'supplier_id' => $this->supplier->id
        ]);

        $response = $this->get(route('inventory.index'));

        $response->assertStatus(200);
        $response->assertViewHas('items');
        $response->assertViewHas('items', function ($items) {
            return $items->count() === 3;
        });
    }

    #[Test]
    public function it_can_create_an_inventory_item()
    {
        $itemData = [
            'name' => 'Test Raw Material',
            'description' => 'A test inventory item',
            'supplier_id' => $this->supplier->id,
            'unit_cost' => 10.50,
            'current_stock' => 100,
            'min_stock_level' => 10,
            'unit' => 'kg',
        ];

        $response = $this->post(route('inventory.store'), $itemData);

        $response->assertRedirect(route('inventory.show', InventoryItem::latest()->first()));
        $this->assertDatabaseHas('inventory_items', $itemData);
    }

    #[Test]
    public function it_can_show_an_inventory_item()
    {
        $item = InventoryItem::factory()->create([
            'supplier_id' => $this->supplier->id
        ]);

        $response = $this->get(route('inventory.show', $item));

        $response->assertStatus(200);
        $response->assertViewHas('item', $item);
    }

    #[Test]
    public function it_can_update_an_inventory_item()
    {
        $item = InventoryItem::factory()->create([
            'supplier_id' => $this->supplier->id
        ]);

        $updatedData = [
            'name' => 'Updated Raw Material',
            'description' => 'Updated description',
            'supplier_id' => $this->supplier->id,
            'unit_cost' => 12.00,
            'current_stock' => 150,
            'min_stock_level' => 15,
            'unit' => 'kg',
        ];

        $response = $this->put(route('inventory.update', $item), $updatedData);

        $response->assertRedirect(route('inventory.show', $item));
        $this->assertDatabaseHas('inventory_items', $updatedData);
    }

    #[Test]
    public function it_can_delete_an_inventory_item()
    {
        $item = InventoryItem::factory()->create([
            'supplier_id' => $this->supplier->id
        ]);

        $response = $this->delete(route('inventory.destroy', $item));

        $response->assertRedirect(route('inventory.index'));
        $this->assertDatabaseMissing('inventory_items', ['id' => $item->id]);
    }

    #[Test]
    public function it_validates_required_fields_when_creating_inventory_item()
    {
        $response = $this->post(route('inventory.store'), []);

        $response->assertSessionHasErrors(['name', 'supplier_id', 'current_stock']);
    }

    #[Test]
    public function it_validates_required_fields_when_updating_inventory_item()
    {
        $item = InventoryItem::factory()->create([
            'supplier_id' => $this->supplier->id
        ]);

        $response = $this->put(route('inventory.update', $item), []);

        $response->assertSessionHasErrors(['name', 'supplier_id', 'current_stock']);
    }

    #[Test]
    public function it_can_perform_inventory_movement()
    {
        $item = InventoryItem::factory()->create([
            'supplier_id' => $this->supplier->id,
            'current_stock' => 100
        ]);

        $movementData = [
            'type' => 'out',
            'qty' => 20,
            'reason' => 'Production usage',
        ];

        $response = $this->post(route('inventory.movement', $item), $movementData);

        $response->assertRedirect(route('inventory.show', $item));
        $this->assertDatabaseHas('stock_movements', [
            'inventory_item_id' => $item->id,
            'type' => 'out',
            'quantity' => 20,
            'reason' => 'Production usage',
        ]);
        $item->refresh();
        $this->assertEquals(80, $item->current_stock);
    }
}
