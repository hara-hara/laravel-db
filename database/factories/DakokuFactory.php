<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dakoku>
 */
class DakokuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'member_id' => $this->faker->numberBetween($min = 1, $max = 2),
            'kubun' => $this->faker->numberBetween($min = 1, $max = 2),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => null,
        ];
    }
}
