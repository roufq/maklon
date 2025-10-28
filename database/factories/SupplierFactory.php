<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'type' => $this->faker->randomElement(['raw_material', 'packaging', 'logistics']),
            'bpom_certified' => $this->faker->boolean(),
            'contact_info' => [
                'email' => $this->faker->unique()->safeEmail(),
                'phone' => $this->faker->phoneNumber(),
                'address' => $this->faker->address(),
            ],
            'rating' => $this->faker->numberBetween(1, 5),
            'performance_score' => $this->faker->randomFloat(2, 0, 100),
            'tenant_id' => 1, // Default tenant
        ];
    }
}
