<?php

namespace Database\Factories;

use App\Models\Agency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Agency>
 */
class AgencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'slug' => fake()->slug(),
            'description' => fake()->sentence(),
            'address' => fake()->address(),
            'opening_hours' => 'Lun–Sam, 9h–19h',
            'whatsapp' => fake()->numerify('221#########'),
            'status' => 'approved',
        ];
    }
}
