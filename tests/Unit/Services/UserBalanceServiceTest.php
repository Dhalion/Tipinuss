<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Exceptions\BetException;
use App\Models\User;
use App\Services\User\UserBalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

final class UserBalanceServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_decrement_balance_reduces_soapnuts(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $service = app(UserBalanceService::class);

        $service->decrementBalance($user, 300);

        $this->assertSame(700, $user->fresh()->soapnuts);
    }

    public function test_decrement_balance_throws_when_insufficient(): void
    {
        $user = User::factory()->withBalance(100)->create();
        $service = app(UserBalanceService::class);

        $this->expectException(BetException::class);

        $service->decrementBalance($user, 200);
    }

    public function test_increment_balance_increases_soapnuts(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $service = app(UserBalanceService::class);

        $service->incrementBalance($user, 500);

        $this->assertSame(1500, $user->fresh()->soapnuts);
    }

    public function test_get_available_balance_returns_correct_value(): void
    {
        $user = User::factory()->withBalance(750)->create();
        $service = app(UserBalanceService::class);

        $balance = $service->getAvailableBalance($user);

        $this->assertSame(750, $balance);
    }
}
