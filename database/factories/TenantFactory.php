<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        $name = $this->faker->company();
        $domain = strtolower(str_replace(' ', '', $name)).'.example.test';
        return [
            'name' => $name,
            'domain' => $domain,
        ];
    }
}

