<?php

declare(strict_types=1);

namespace Tests\Integration\Livewire\Admin;

use App\Livewire\Page\Admin\OrganisationManagement;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;

final class OrganisationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(OrganisationManagement::class)
            ->assertOk();
    }

    public function test_non_admin_cannot_render(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        Livewire::actingAs($user)
            ->test(OrganisationManagement::class)
            ->assertForbidden();
    }

    public function test_admin_can_create_organisation(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(OrganisationManagement::class)
            ->set('newOrganisationName', 'Test Organisation')
            ->call('createOrganisation')
            ->assertHasNoErrors()
            ->assertSet('newOrganisationName', '')
            ->assertSet('showCreateForm', false);

        $this->assertDatabaseHas('organisations', ['name' => 'Test Organisation']);
    }

    public function test_create_organisation_fails_with_duplicate_name(): void
    {
        $admin = User::factory()->admin()->create();
        Organisation::factory()->create(['name' => 'Test Organisation']);

        Livewire::actingAs($admin)
            ->test(OrganisationManagement::class)
            ->set('newOrganisationName', 'Test Organisation')
            ->call('createOrganisation')
            ->assertHasErrors('newOrganisationName');
    }

    public function test_create_organisation_fails_with_empty_name(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(OrganisationManagement::class)
            ->call('createOrganisation')
            ->assertHasErrors('newOrganisationName');
    }

    public function test_admin_can_delete_organisation(): void
    {
        $admin = User::factory()->admin()->create();
        $organisation = Organisation::factory()->create();

        Livewire::actingAs($admin)
            ->test(OrganisationManagement::class)
            ->call('deleteOrganisation', $organisation->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('organisations', ['id' => $organisation->id]);
    }
}
