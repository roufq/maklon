<?php

namespace Database\Factories;

use App\Models\BpomRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

class BpomRegistrationFactory extends Factory
{
    protected $model = BpomRegistration::class;

    public function definition(): array
    {
        return [
            'product_name' => $this->faker->word(),
            'registration_number' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{8}'),
            'approval_date' => $this->faker->date(),
            'expiry_date' => $this->faker->dateTimeBetween('+1 year', '+5 years'),
            'status' => $this->faker->randomElement(['active', 'expired', 'revoked', 'pending']),
            'document_path' => $this->faker->filePath(),
            'tenant_id' => 1, // Default tenant
        ];
    }
}
