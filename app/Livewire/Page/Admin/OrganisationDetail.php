<?php

declare(strict_types=1);

namespace App\Livewire\Page\Admin;

use App\Models\Organisation;
use App\Repositories\Contracts\OrganisationRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

final class OrganisationDetail extends Component
{
    #[Locked]
    public string $orgId;

    public string $selectedUserId = '';

    public function mount(Organisation $organisation): void
    {
        $this->authorize('admin');
        $this->orgId = $organisation->id;
    }

    #[Computed]
    public function organisation(): Organisation
    {
        return app(OrganisationRepositoryInterface::class)->findById($this->orgId);
    }

    #[Computed]
    public function unassignedUsers(): Collection
    {
        return app(UserRepositoryInterface::class)->all()
            ->filter(fn ($user) => $user->organisation_id === null);
    }

    public function removeMember(string $userId, UserRepositoryInterface $users): void
    {
        $user = $users->findById($userId);
        if ($user === null || $user->organisation_id !== $this->orgId) {
            return;
        }

        $user->organisation_id = null;
        $users->save($user);

        unset($this->organisation, $this->unassignedUsers);

        Flux::toast(variant: 'success', text: __('admin.organisations.member_removed'));
    }

    public function addMember(OrganisationRepositoryInterface $organisations, UserRepositoryInterface $users): void
    {
        if ($this->selectedUserId === '') {
            return;
        }

        $user = $users->findById($this->selectedUserId);
        if ($user === null) {
            return;
        }

        $organisation = $organisations->findById($this->orgId);
        if ($organisation === null) {
            return;
        }

        $user->organisation_id = $organisation->id;
        $users->save($user);

        $this->selectedUserId = '';
        unset($this->organisation, $this->unassignedUsers);

        Flux::toast(variant: 'success', text: __('admin.organisations.member_added'));
    }

    public function deleteOrganisation(OrganisationRepositoryInterface $organisations): void
    {
        $organisation = $organisations->findById($this->orgId);
        if ($organisation === null) {
            return;
        }

        $organisations->delete($organisation);

        Flux::toast(variant: 'success', text: __('admin.organisations.deleted'));

        $this->redirect(route('admin.organisations'), navigate: true);
    }

    public function render(): View
    {
        return view('pages.admin.organisation-detail', [
            'organisation' => $this->organisation(),
            'unassignedUsers' => $this->unassignedUsers(),
        ]);
    }
}
