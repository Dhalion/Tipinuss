<?php

declare(strict_types=1);

namespace Tests\Integration\Livewire;

use App\Livewire\SoapnutsBalance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;

final class SoapnutsBalanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_balance(): void
    {
        $user = User::factory()->withBalance(500)->create();

        Livewire::actingAs($user)
            ->test(SoapnutsBalance::class)
            ->assertOk();
    }

    public function test_refresh_dispatches_nothing(): void
    {
        $user = User::factory()->withBalance(500)->create();

        Livewire::actingAs($user)
            ->test(SoapnutsBalance::class)
            ->call('refresh')
            ->assertOk();
    }
}
