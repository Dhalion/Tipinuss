<?php

declare(strict_types=1);

namespace Tests\Integration\Livewire\Auth;

use App\Livewire\Page\Auth\PendingApproval;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;

final class PendingApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_for_unapproved_user(): void
    {
        $user = User::factory()->unapproved()->create();

        Livewire::actingAs($user)
            ->test(PendingApproval::class)
            ->assertOk()
            ->assertSee(__('auth.pending_title'));
    }

    public function test_redirects_when_approved(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(PendingApproval::class)
            ->call('checkStatus')
            ->assertRedirect(route('main'));
    }

    public function test_does_not_redirect_when_unapproved(): void
    {
        $user = User::factory()->unapproved()->create();

        Livewire::actingAs($user)
            ->test(PendingApproval::class)
            ->call('checkStatus')
            ->assertNoRedirect();
    }
}
