<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\BetStatus;
use App\Exceptions\BetException;
use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;
use App\Services\Betting\BettingValidationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase;

final class BettingValidationServiceTest extends TestCase
{
    private BettingValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new BettingValidationService;
    }

    public function test_validate_bet_is_open_passes_for_open_bet(): void
    {
        $bet = new Bet(['status' => BetStatus::Open, 'expires_at' => Carbon::now()->addDay()]);

        $this->service->validateBetIsOpen($bet);

        $this->expectNotToPerformAssertions();
    }

    public function test_validate_bet_is_open_throws_when_closed(): void
    {
        $bet = new Bet(['status' => BetStatus::Closed, 'expires_at' => null]);

        $this->expectException(BetException::class);
        $this->expectExceptionMessage(__('bets.bet_closed_error'));

        $this->service->validateBetIsOpen($bet);
    }

    public function test_validate_bet_is_open_throws_when_expired(): void
    {
        $bet = new Bet(['status' => BetStatus::Open, 'expires_at' => Carbon::now()->subDay()]);

        $this->expectException(BetException::class);
        $this->expectExceptionMessage(__('bets.bet_expired_error'));

        $this->service->validateBetIsOpen($bet);
    }

    public function test_validate_balance_sufficient_passes_with_enough_balance(): void
    {
        $user = new User(['soapnuts' => 500]);

        $this->service->validateBalanceSufficient($user, 100);

        $this->expectNotToPerformAssertions();
    }

    public function test_validate_balance_sufficient_throws_when_insufficient(): void
    {
        $user = new User(['soapnuts' => 50]);

        $this->expectException(BetException::class);

        $this->service->validateBalanceSufficient($user, 100);
    }

    public function test_validate_amount_within_bounds_passes(): void
    {
        $this->service->validateAmountWithinBounds(50);

        $this->expectNotToPerformAssertions();
    }

    public function test_validate_amount_within_bounds_throws_below_min(): void
    {
        $this->expectException(BetException::class);
        $this->expectExceptionMessage(__('bets.amount_out_of_bounds', ['min' => 1, 'max' => 100000]));

        $this->service->validateAmountWithinBounds(0);
    }

    public function test_validate_amount_within_bounds_throws_above_max(): void
    {
        $this->expectException(BetException::class);
        $this->expectExceptionMessage(__('bets.amount_out_of_bounds', ['min' => 1, 'max' => 100000]));

        $this->service->validateAmountWithinBounds(100_001);
    }

    public function test_validate_option_exists_passes(): void
    {
        $option = new BetOption;

        $this->service->validateOptionExists($option);

        $this->expectNotToPerformAssertions();
    }

    public function test_validate_option_exists_throws_when_null(): void
    {
        $this->expectException(BetException::class);
        $this->expectExceptionMessage(__('bets.option_not_found'));

        $this->service->validateOptionExists(null);
    }

    public function test_can_close_bet_admin_always_allowed(): void
    {
        $bet = new Bet(['status' => BetStatus::Open, 'expires_at' => null]);
        $admin = new User;
        $admin->forceFill(['is_admin' => true]);

        $result = $this->service->canCloseBet($bet, $admin, 0);

        $this->assertTrue($result);
    }

    public function test_can_close_bet_non_admin_needs_two_bettors(): void
    {
        $bet = new Bet(['status' => BetStatus::Open, 'expires_at' => null]);
        $user = new User;
        $user->forceFill(['is_admin' => false]);

        $this->assertFalse($this->service->canCloseBet($bet, $user, 0));
        $this->assertFalse($this->service->canCloseBet($bet, $user, 1));
        $this->assertTrue($this->service->canCloseBet($bet, $user, 2));
        $this->assertTrue($this->service->canCloseBet($bet, $user, 5));
    }

    public function test_can_close_bet_returns_false_when_bet_is_closed(): void
    {
        $bet = new Bet(['status' => BetStatus::Closed, 'expires_at' => null]);
        $admin = new User;
        $admin->forceFill(['is_admin' => true]);

        $this->assertFalse($this->service->canCloseBet($bet, $admin, 0));
    }
}
