<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Bet;
use App\Models\BetOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BetOption>
 */
final class BetOptionFactory extends Factory
{
    protected $model = BetOption::class;

    public function definition(): array
    {
        $odds = fake()->randomFloat(2, 1.5, 10.0);

        return [
            'bet_id' => Bet::factory(),
            'title' => fake()->word(),
            'odds' => $odds,
            'base_odds' => $odds,
        ];
    }

    public function winning(): static
    {
        return $this->state(fn (array $attributes) => [
            'result' => true,
        ]);
    }

    public function losing(): static
    {
        return $this->state(fn (array $attributes) => [
            'result' => false,
        ]);
    }
}
