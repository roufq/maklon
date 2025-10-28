<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;
use App\Models\User;
use App\Models\Task;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResourceAllocation>
 */
class ResourceAllocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+30 days');
        $endDate = $this->faker->dateTimeBetween($startDate, $startDate->format('Y-m-d H:i:s') . ' +30 days');

        return [
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'task_id' => Task::factory(),
            'allocated_hours' => $this->faker->randomFloat(1, 1, 24),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'allocation_type' => $this->faker->randomElement(['planned', 'actual', 'forecast']),
            'notes' => $this->faker->optional(0.7)->sentence(),
        ];
    }
}
