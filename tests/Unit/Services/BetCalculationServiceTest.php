<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\BetOption;
use App\Services\Betting\BetCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

final class BetCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculate_potential_winnings(): void
    {
        $option = BetOption::factory()->create(['odds' => 2.50]);
        $service = app(BetCalculationService::class);

        $winnings = $service->calculatePotentialWinnings($option, 100);

        $this->assertSame(250, $winnings);
    }

    public function test_calculate_potential_winnings_rounds_half_up(): void
    {
        $option = BetOption::factory()->create(['odds' => 1.5]);
        $service = app(BetCalculationService::class);

        $winnings = $service->calculatePotentialWinnings($option, 99);

        $this->assertSame(149, $winnings);
    }
}
