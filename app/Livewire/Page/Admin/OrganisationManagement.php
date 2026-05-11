<?php

declare(strict_types=1);

namespace App\Livewire\Page\Admin;

use App\Models\Organisation;
use App\Repositories\Contracts\OrganisationRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class OrganisationManagement extends Component
{
    public string $newOrganisationName = '';

    public function mount(): void
    {
        $this->authorize('admin');
    }

    public function createOrganisation(OrganisationRepositoryInterface $organisations): void
    {
        $this->validate(['newOrganisationName' => 'required|min:2|max:100']);

        if ($organisations->existsByName($this->newOrganisationName)) {
            $this->addError('newOrganisationName', 'Organisation existiert bereits.');

            return;
        }

        $organisation = new Organisation(['name' => $this->newOrganisationName]);
        $organisations->save($organisation);

        $this->newOrganisationName = '';
    }

    public function assignUserToOrganisation(
        string $userId,
        ?string $organisationId,
        OrganisationRepositoryInterface $organisations,
        UserRepositoryInterface $users,
    ): void {
        $user = $users->findById($userId);
        if ($user === null) {
            return;
        }

        $resolvedOrganisationId = null;
        if ($organisationId !== null && $organisationId !== '') {
            $organisation = $organisations->findById($organisationId);
            if ($organisation === null) {
                return;
            }
            $resolvedOrganisationId = $organisation->id;
        }

        $user->organisation_id = $resolvedOrganisationId;
        $users->save($user);
    }

    public function deleteOrganisation(string $organisationId, OrganisationRepositoryInterface $organisations): void
    {
        $organisation = $organisations->findById($organisationId);
        if ($organisation === null) {
            return;
        }

        $organisations->delete($organisation);
    }

    public function render(
        OrganisationRepositoryInterface $organisations,
        UserRepositoryInterface $users,
    ): View {
        return view('pages.admin.organisations', [
            'organisations' => $organisations->findAll(),
            'allUsers' => $users->all(),
        ]);
    }
}
