<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Holiday>
 */
class HolidayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->randomElement(['Summer Vacation', 'Winter Break', 'Teachers Day', 'Local Holiday', 'Eid-ul-Fitr', 'Victory Day']),
            'date' => $this->faker->unique()->date('Y-m-d', '2026-12-31'),
        ];
    }
}
