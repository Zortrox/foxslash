<?php

namespace Database\Factories;

use App\Models\SteamGame;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SteamGame>
 */
class SteamGameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'appid' => fake()->unique()->numberBetween(10, 3_000_000),
            'name' => fake()->unique()->words(3, true),
        ];
    }
}
