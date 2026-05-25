<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BetaAccessKey;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BetaAccessKey>
 */
final class BetaAccessKeyFactory extends Factory
{
    protected $model = BetaAccessKey::class;

    public function definition(): array
    {
        return [
            'key' => strtoupper(fake()->bothify('????-####')),
            'organisation_id' => Organisation::factory(),
            'created_by_user_id' => User::factory(),
            'is_active' => true,
            'expires_at' => now()->addYear(),
            'start_balance' => null,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function used(): static
    {
        return $this->state(fn (array $attributes) => [
            'used_at' => now()->subDay(),
            'used_by_user_id' => User::factory(),
        ]);
    }

    public function withStartBalance(int $balance): static
    {
        return $this->state(fn (array $attributes) => [
            'start_balance' => $balance,
        ]);
    }
}
