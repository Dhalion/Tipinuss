<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BetStatus;
use App\Models\Bet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Bet>
 */
final class BetFactory extends Factory
{
    protected $model = Bet::class;

    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->optional()->paragraph(),
            'status' => BetStatus::Open,
            'expires_at' => fake()->optional(0.7)->dateTimeBetween('+1 day', '+30 days'),
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BetStatus::Closed,
            'closed_at' => now()->subHour(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }
}
