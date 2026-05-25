<?php

declare(strict_types=1);

namespace Tests\Integration\Livewire\Admin;

use App\Enums\TransactionType;
use App\Livewire\Page\Admin\UserManagement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;

final class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->assertOk();
    }

    public function test_non_admin_cannot_render(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        Livewire::actingAs($user)
            ->test(UserManagement::class)
            ->assertForbidden();
    }

    public function test_set_filter(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('setFilter', 'pending')
            ->assertSet('approvalFilter', 'pending')
            ->call('setFilter', 'approved')
            ->assertSet('approvalFilter', 'approved')
            ->call('setFilter', '')
            ->assertSet('approvalFilter', '');
    }

    public function test_adjusts_balance(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->withBalance(500)->create();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->set('balanceAdjustments', [$target->id => 200])
            ->call('adjustBalance', $target->id)
            ->assertHasNoErrors();

        $this->assertSame(700, $target->fresh()->soapnuts);
        $this->assertDatabaseHas('balance_transactions', [
            'user_id' => $target->id,
            'type' => TransactionType::AdminAdjustment->value,
            'amount' => 200,
        ]);
    }

    public function test_approves_user(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->unapproved()->create();

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('approveUser', $target->id, null)
            ->assertHasNoErrors();

        $this->assertTrue($target->fresh()->is_approved);
    }

    public function test_toggles_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create(['is_admin' => false]);

        Livewire::actingAs($admin)
            ->test(UserManagement::class)
            ->call('toggleAdmin', $target->id)
            ->assertHasNoErrors();

        $this->assertTrue($target->fresh()->isAdmin());
    }
}
