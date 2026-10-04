<?php

namespace Database\Factories;

use App\Models\AgencyRoute;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AgencyRoute>
 */
class AgencyRouteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $countries = ['SN', 'KM', 'FR'];
        $from = fake()->randomElement($countries);
        $to = fake()->randomElement(array_diff($countries, [$from]));

        return [
            'from_country' => $from,
            'to_country' => $to,
        ];
    }
}
