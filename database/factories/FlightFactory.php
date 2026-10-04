<?php

namespace Database\Factories;

use App\Models\Flight;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Flight>
 */
class FlightFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $flightDate = fake()->dateTimeBetween('+1 week', '+3 months');

        return [
            'from_country' => fake()->randomElement(['SN', 'KM', 'FR']),
            'to_country' => fake()->randomElement(['SN', 'KM', 'FR']),
            'flight_date' => $flightDate,
            'airline' => fake()->randomElement(['Air Sénégal', 'Air France', 'Ethiopian', 'Air Austral']),
            'total_kg' => fake()->numberBetween(50, 150),
            'remaining_kg' => fake()->numberBetween(0, 50),
            'price_per_kg' => fake()->randomFloat(2, 8, 12),
            'currency' => fake()->randomElement(['FCFA', 'EUR']),
            'drop_off_deadline' => (clone $flightDate)->modify('-1 day'),
            'status' => 'open',
        ];
    }
}
