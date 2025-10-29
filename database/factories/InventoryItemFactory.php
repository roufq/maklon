<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryItemFactory extends Factory
{
    protected $model = InventoryItem::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . ' Raw Material',
            'supplier_id' => Supplier::factory(),
            'unit_cost' => $this->faker->randomFloat(2, 1, 100),
            'current_stock' => $this->faker->numberBetween(0, 1000),
            'min_stock' => $this->faker->numberBetween(10, 100),
            'unit' => $this->faker->randomElement(['kg', 'pcs', 'liter', 'box']),
            'tenant_id' => 1, // Default tenant
        ];
    }
}
