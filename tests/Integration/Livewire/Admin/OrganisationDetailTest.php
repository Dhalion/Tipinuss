<?php

declare(strict_types=1);

namespace Tests\Integration\Livewire\Admin;

use App\Livewire\Page\Admin\OrganisationDetail;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;

final class OrganisationDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render(): void
    {
        $admin = User::factory()->admin()->create();
        $organisation = Organisation::factory()->create();

        Livewire::actingAs($admin)
            ->test(OrganisationDetail::class, ['organisation' => $organisation])
            ->assertOk();
    }

    public function test_non_admin_cannot_render(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $organisation = Organisation::factory()->create();

        Livewire::actingAs($user)
            ->test(OrganisationDetail::class, ['organisation' => $organisation])
            ->assertForbidden();
    }

    public function test_admin_can_remove_member(): void
    {
        $admin = User::factory()->admin()->create();
        $organisation = Organisation::factory()->create();
        $member = User::factory()->create(['organisation_id' => $organisation->id]);

        Livewire::actingAs($admin)
            ->test(OrganisationDetail::class, ['organisation' => $organisation])
            ->call('removeMember', $member->id)
            ->assertHasNoErrors();

        $this->assertNull($member->fresh()->organisation_id);
    }

    public function test_admin_can_add_member(): void
    {
        $admin = User::factory()->admin()->create();
        $organisation = Organisation::factory()->create();
        $unassigned = User::factory()->create(['organisation_id' => null]);

        Livewire::actingAs($admin)
            ->test(OrganisationDetail::class, ['organisation' => $organisation])
            ->set('selectedUserId', $unassigned->id)
            ->call('addMember')
            ->assertHasNoErrors()
            ->assertSet('selectedUserId', '');

        $this->assertSame($organisation->id, $unassigned->fresh()->organisation_id);
    }

    public function test_admin_can_delete_organisation(): void
    {
        $admin = User::factory()->admin()->create();
        $organisation = Organisation::factory()->create();

        Livewire::actingAs($admin)
            ->test(OrganisationDetail::class, ['organisation' => $organisation])
            ->call('deleteOrganisation')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.organisations'));

        $this->assertDatabaseMissing('organisations', ['id' => $organisation->id]);
    }
}
