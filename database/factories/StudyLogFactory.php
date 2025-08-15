<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudyLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'studied_at' => fake()->date(),
            'subject' => fake()->randomElement(['Laravel', '設計', 'フロント', 'アプリ', 'ビジネススキル']).' '.fake()->word(),
            'duration_minutes' => fake()->numberBetween(15, 240),
            'notes' => fake()->optional()->sentence(10),
        ];
    }
}
