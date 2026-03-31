<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::first() ?? User::factory();
        $date = $this->faker->dateTimeBetween('-30 days', 'now');
        
        // Realistic 9 AM In, 4 PM Out
        $punchIn = clone $date;
        $punchIn->setTime(mt_rand(8, 10), mt_rand(0, 59), 0);
        
        $punchOut = clone $date;
        $punchOut->setTime(mt_rand(15, 17), mt_rand(0, 59), 0);

        return [
            'user_id' => $user->id,
            'date' => $date->format('Y-m-d'),
            'check_in' => $punchIn->format('H:i:s'),
            'check_out' => $punchOut->format('H:i:s'),
            'status' => 'Present',
        ];
    }
}
