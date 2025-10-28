<?php

namespace Database\Factories;

use App\Models\QcResult;
use App\Models\ProductionBatch;
use App\Models\QualityCheckpoint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QcResultFactory extends Factory
{
    protected $model = QcResult::class;

    public function definition(): array
    {
        return [
            'production_batch_id' => ProductionBatch::factory(),
            'quality_checkpoint_id' => QualityCheckpoint::factory(),
            'status' => $this->faker->randomElement(['pass', 'fail']),
            'notes' => $this->faker->optional(0.7)->sentence(), // 70% chance of having notes
            'by_user_id' => User::factory(),
        ];
    }
}
