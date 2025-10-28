<?php

namespace Database\Factories;

use App\Models\ProductionBatch;
use App\Models\Project;
use App\Models\BpomRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductionBatchFactory extends Factory
{
    protected $model = ProductionBatch::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'bpom_registration_id' => BpomRegistration::factory(),
            'batch_number' => $this->faker->unique()->regexify('[A-Z]{4}-[0-9]{4}'),
            'quantity_produced' => $this->faker->numberBetween(100, 10000),
            'expiry_date' => $this->faker->dateTimeBetween('+6 months', '+2 years'),
            'qc_status' => $this->faker->randomElement(['pending', 'passed', 'failed']),
            'tenant_id' => 1, // Default tenant
        ];
    }
}
