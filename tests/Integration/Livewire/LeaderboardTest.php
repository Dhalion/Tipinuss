<?php

declare(strict_types=1);

namespace Tests\Integration\Livewire;

use App\Livewire\Bets\Leaderboard;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;

final class LeaderboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_shows_top_bettors_for_guest(): void
    {
        $userA = User::factory()->withBalance(500)->create(['name' => 'Alice']);
        $userB = User::factory()->withBalance(300)->create(['name' => 'Bob']);

        Livewire::test(Leaderboard::class)
            ->assertOk()
            ->assertSee('Alice')
            ->assertSee('Bob');
    }

    public function test_shows_top_bettors_for_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $userA = User::factory()->withBalance(500)->create(['name' => 'Alice']);
        $userB = User::factory()->withBalance(300)->create(['name' => 'Bob']);

        Livewire::actingAs($admin)
            ->test(Leaderboard::class)
            ->assertOk()
            ->assertSee('Alice')
            ->assertSee('Bob');
    }

    public function test_filters_by_organisation_for_regular_user(): void
    {
        $orgA = Organisation::factory()->create();
        $orgB = Organisation::factory()->create();

        $user = User::factory()->withBalance(100)->create([
            'name' => 'CurrentUser',
            'organisation_id' => $orgA->id,
        ]);

        User::factory()->withBalance(500)->create([
            'name' => 'OrgAMember',
            'organisation_id' => $orgA->id,
        ]);

        User::factory()->withBalance(300)->create([
            'name' => 'OrgBMember',
            'organisation_id' => $orgB->id,
        ]);

        Livewire::actingAs($user)
            ->test(Leaderboard::class)
            ->assertOk()
            ->assertSee('CurrentUser')
            ->assertSee('OrgAMember')
            ->assertDontSee('OrgBMember');
    }

    public function test_renders_empty_state(): void
    {
        Livewire::test(Leaderboard::class)
            ->assertOk();
    }
}
