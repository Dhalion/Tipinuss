<?php

declare(strict_types=1);

namespace Tests\Integration\Livewire\Admin;

use App\Livewire\Page\Admin\BetaKeyManagement;
use App\Models\BetaAccessKey;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;

final class BetaKeyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(BetaKeyManagement::class)
            ->assertOk();
    }

    public function test_non_admin_cannot_render(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        Livewire::actingAs($user)
            ->test(BetaKeyManagement::class)
            ->assertForbidden();
    }

    public function test_admin_can_create_beta_key(): void
    {
        $admin = User::factory()->admin()->create();
        $organisation = Organisation::factory()->create();

        Livewire::actingAs($admin)
            ->test(BetaKeyManagement::class)
            ->set('organisationId', $organisation->id)
            ->call('create')
            ->assertHasNoErrors()
            ->assertDispatched('beta-key-created');

        $this->assertDatabaseHas('beta_access_keys', [
            'organisation_id' => $organisation->id,
            'created_by_user_id' => $admin->id,
            'is_active' => true,
        ]);
    }

    public function test_create_beta_key_fails_without_organisation(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(BetaKeyManagement::class)
            ->call('create')
            ->assertHasErrors('organisationId');
    }

    public function test_create_beta_key_fails_with_expired_date(): void
    {
        $admin = User::factory()->admin()->create();
        $organisation = Organisation::factory()->create();

        Livewire::actingAs($admin)
            ->test(BetaKeyManagement::class)
            ->set('organisationId', $organisation->id)
            ->set('expiresAt', now()->subDay()->format('Y-m-d'))
            ->call('create')
            ->assertHasErrors('expiresAt');
    }

    public function test_admin_can_deactivate_key(): void
    {
        $admin = User::factory()->admin()->create();
        $organisation = Organisation::factory()->create();
        $key = BetaAccessKey::factory()->create([
            'organisation_id' => $organisation->id,
            'created_by_user_id' => $admin->id,
        ]);

        Livewire::actingAs($admin)
            ->test(BetaKeyManagement::class)
            ->call('deactivate', $key->id)
            ->assertHasNoErrors();

        $this->assertFalse($key->fresh()->is_active);
    }

    public function test_sort_toggles_direction(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(BetaKeyManagement::class)
            ->call('sort', 'key')
            ->assertSet('sortBy', 'key')
            ->assertSet('sortDirection', 'asc')
            ->call('sort', 'key')
            ->assertSet('sortBy', 'key')
            ->assertSet('sortDirection', 'desc');
    }
}
