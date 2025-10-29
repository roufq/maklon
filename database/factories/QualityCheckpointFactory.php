<?php

namespace Database\Factories;

use App\Models\QualityCheckpoint;
use Illuminate\Database\Eloquent\Factories\Factory;

class QualityCheckpointFactory extends Factory
{
    protected $model = QualityCheckpoint::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'criteria' => ['temperature' => '25-30°C', 'humidity' => '<50%'],
            'required' => $this->faker->boolean(80), // 80% chance of being required
            'tenant_id' => 1, // Default tenant
        ];
    }
}
