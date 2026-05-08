<?php

declare(strict_types=1);

namespace App\Livewire\Page\Admin;

use App\Models\Organisation;
use App\Repositories\Contracts\OrganisationRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

final class OrganisationManagement extends Component
{
    public string $newOrganisationName = '';

    /** @var Collection<int, Organisation> */
    public Collection $organisations;

    public function mount(
        OrganisationRepositoryInterface $organisations,
    ): void {
        $this->authorize('admin');
        $this->organisations = $organisations->findAll();
    }

    public function createOrganisation(OrganisationRepositoryInterface $organisations): void
    {
        $this->validate(['newOrganisationName' => 'required|min:2|max:100|unique:organisations,name']);

        $organisation = new Organisation(['name' => $this->newOrganisationName]);
        $organisations->save($organisation);

        $this->newOrganisationName = '';
        $this->organisations = $organisations->findAll();
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

        $this->organisations = $organisations->findAll();
    }

    public function deleteOrganisation(string $organisationId, OrganisationRepositoryInterface $organisations): void
    {
        $organisation = $organisations->findById($organisationId);
        if ($organisation === null) {
            return;
        }

        $organisations->delete($organisation);
        $this->organisations = $organisations->findAll();
    }

    public function render(UserRepositoryInterface $users): View
    {
        return view('pages.admin.organisations', [
            'allUsers' => $users->all(),
        ]);
    }
}
