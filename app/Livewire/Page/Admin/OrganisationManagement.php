<?php

declare(strict_types=1);

namespace App\Livewire\Page\Admin;

use App\Models\Organisation;
use App\Repositories\Contracts\OrganisationRepositoryInterface;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class OrganisationManagement extends Component
{
    public string $newOrganisationName = '';

    public bool $showCreateForm = false;

    public function mount(): void
    {
        $this->authorize('admin');
    }

    public function createOrganisation(OrganisationRepositoryInterface $organisations): void
    {
        $this->validate(['newOrganisationName' => 'required|min:2|max:100']);

        if ($organisations->existsByName($this->newOrganisationName)) {
            $this->addError('newOrganisationName', __('admin.organisations.exists_error'));

            return;
        }

        $organisation = new Organisation(['name' => $this->newOrganisationName]);
        $organisations->save($organisation);

        $this->newOrganisationName = '';
        $this->showCreateForm = false;
    }

    public function deleteOrganisation(string $organisationId, OrganisationRepositoryInterface $organisations): void
    {
        $organisation = $organisations->findById($organisationId);
        if ($organisation === null) {
            return;
        }

        $organisations->delete($organisation);
    }

    public function render(OrganisationRepositoryInterface $organisations): View
    {
        return view('pages.admin.organisations', [
            'organisations' => $organisations->findAll(),
        ]);
    }
}
