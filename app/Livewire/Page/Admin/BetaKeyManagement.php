<?php

declare(strict_types=1);

namespace App\Livewire\Page\Admin;

use App\Actions\Auth\CreateBetaAccessKeyAction;
use App\Repositories\Contracts\BetaAccessKeyRepositoryInterface;
use App\Repositories\Contracts\OrganisationRepositoryInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class BetaKeyManagement extends Component
{
    #[Validate('required')]
    public string $organisationId = '';

    #[Validate('nullable|string|max:32|unique:beta_access_keys,key')]
    public string $customKey = '';

    #[Validate('nullable|date|after:today')]
    public ?string $expiresAt = null;

    public bool $showCreateForm = false;

    public function mount(): void
    {
        $this->authorize('admin');
    }

    public function create(CreateBetaAccessKeyAction $action): void
    {
        $this->authorize('admin');
        $this->validate();

        $action->execute(
            admin: Auth::user(),
            organisationId: $this->organisationId,
            customKey: $this->customKey !== '' ? $this->customKey : null,
            expiresAt: $this->expiresAt,
        );

        $this->resetForm();
        $this->dispatch('beta-key-created');
    }

    public function deactivate(string $keyId, BetaAccessKeyRepositoryInterface $betaKeys): void
    {
        $this->authorize('admin');

        $key = $betaKeys->findById($keyId);
        if ($key === null) {
            return;
        }

        $key->is_active = false;
        $betaKeys->save($key);
    }

    private function resetForm(): void
    {
        $this->organisationId = '';
        $this->customKey = '';
        $this->expiresAt = null;
        $this->showCreateForm = false;
    }

    public function render(
        BetaAccessKeyRepositoryInterface $betaKeys,
        OrganisationRepositoryInterface $organisations,
    ): View {
        $keys = $betaKeys->all();
        $keys->loadMissing('organisation');

        return view('pages.admin.beta-keys', [
            'keys' => $keys,
            'organisations' => $organisations->findAll(),
        ]);
    }
}
