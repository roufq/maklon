<?php

namespace Database\Factories;

use App\Models\Delivery;
use App\Models\ProductionBatch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryFactory extends Factory
{
    protected $model = Delivery::class;

    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::factory(),
            'production_batch_id' => ProductionBatch::factory(),
            'customer_id' => User::factory(),
            'shipping_address' => $this->faker->address(),
            'tracking_number' => $this->faker->optional()->regexify('[A-Z]{3}[0-9]{9}'),
            'status' => $this->faker->randomElement(['ready', 'shipped', 'delivered', 'returned']),
            'delivered_at' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'notes' => $this->faker->optional()->sentence(),
            'tenant_id' => 1, // Default tenant
        ];
    }
}
