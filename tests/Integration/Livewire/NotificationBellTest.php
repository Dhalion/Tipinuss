<?php

declare(strict_types=1);

namespace Tests\Integration\Livewire;

use App\Livewire\NotificationBell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Str;
use Livewire\Livewire;

final class NotificationBellTest extends TestCase
{
    use RefreshDatabase;

    public function test_displays_unread_notifications(): void
    {
        $user = User::factory()->create();

        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\Notifications\BetClosed',
            'data' => [
                'bet_title' => 'Test notification',
                'bet_slug_url' => 'test-slug',
                'user_bet_status' => 'won',
                'amount_wagered' => 100,
                'potential_winnings' => 200,
            ],
        ]);

        Livewire::actingAs($user)
            ->test(NotificationBell::class)
            ->assertOk();
    }

    public function test_mark_as_read(): void
    {
        $user = User::factory()->create();

        $notification = $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\Notifications\BetClosed',
            'data' => [
                'bet_title' => 'Test notification',
                'bet_slug_url' => 'test-slug',
                'user_bet_status' => 'won',
                'amount_wagered' => 100,
                'potential_winnings' => 200,
            ],
        ]);

        $this->assertNull($notification->read_at);

        Livewire::actingAs($user)
            ->test(NotificationBell::class)
            ->call('markAsRead', $notification->id);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_mark_as_read_dispatches_event(): void
    {
        $user = User::factory()->create();

        $notification = $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\Notifications\BetClosed',
            'data' => [
                'bet_title' => 'Test notification',
                'bet_slug_url' => 'test-slug',
                'user_bet_status' => 'won',
                'amount_wagered' => 100,
                'potential_winnings' => 200,
            ],
        ]);

        Livewire::actingAs($user)
            ->test(NotificationBell::class)
            ->call('markAsRead', $notification->id)
            ->assertDispatched('notification-read');
    }

    public function test_handles_nonexistent_notification_gracefully(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(NotificationBell::class)
            ->call('markAsRead', (string) Str::uuid())
            ->assertOk();
    }
}
