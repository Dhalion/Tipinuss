<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\UserBetStatus;
use App\Models\BetOption;
use App\Models\User;
use App\Models\UserBet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserBet>
 */
final class UserBetFactory extends Factory
{
    protected $model = UserBet::class;

    public function definition(): array
    {
        $amountWagered = fake()->numberBetween(10, 500);
        $odds = fake()->randomFloat(2, 1.5, 5.0);

        return [
            'user_id' => User::factory(),
            'bet_option_id' => BetOption::factory(),
            'amount_wagered' => $amountWagered,
            'potential_winnings' => (int) round($odds * $amountWagered),
            'status' => UserBetStatus::Pending,
        ];
    }

    public function won(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserBetStatus::Won,
        ]);
    }

    public function lost(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserBetStatus::Lost,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => UserBetStatus::Pending,
        ]);
    }
}
