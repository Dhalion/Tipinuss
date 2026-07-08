<?php

declare(strict_types=1);

namespace Tests\Integration\Livewire;

use App\Livewire\Page\MainPage;
use App\Models\Bet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;

final class MainPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_for_guest(): void
    {
        Livewire::test(MainPage::class)
            ->assertOk();
    }

    public function test_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(MainPage::class)
            ->assertOk();
    }

    public function test_shows_registration_toast_when_in_session(): void
    {
        Session::put('_registration_toast', [
            'balance' => 500,
            'message' => null,
        ]);

        Livewire::test(MainPage::class);

        $this->assertNull(Session::get('_registration_toast'));
    }

    public function test_shows_registration_toast_with_message(): void
    {
        Session::put('_registration_toast', [
            'balance' => 500,
            'message' => 'Extra info',
        ]);

        Livewire::test(MainPage::class)
            ->assertOk();

        $this->assertNull(Session::get('_registration_toast'));
    }

    public function test_shows_recent_bets(): void
    {
        $bets = Bet::factory()->count(3)->create();

        $component = Livewire::test(MainPage::class);

        foreach ($bets as $bet) {
            $component->assertSee($bet->title);
        }
    }
}
