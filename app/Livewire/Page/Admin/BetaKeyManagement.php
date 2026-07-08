<?php

declare(strict_types=1);

namespace App\Livewire\Page\Admin;

use App\Actions\Auth\CreateBetaAccessKeyAction;
use App\Constants\AppDefaults;
use App\Repositories\Contracts\BetaAccessKeyRepositoryInterface;
use App\Repositories\Contracts\OrganisationRepositoryInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

final class BetaKeyManagement extends Component
{
    use WithPagination;

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    #[Validate('required')]
    public string $organisationId = '';

    #[Validate(['nullable', 'string', 'max:'.AppDefaults::BETA_KEY_MAX_LENGTH, 'unique:beta_access_keys,key'])]
    public string $customKey = '';

    #[Validate('nullable|date|after:today')]
    public ?string $expiresAt = null;

    #[Validate('nullable|integer|min:0')]
    public ?int $startBalance = null;

    #[Validate(['nullable', 'string', 'max:'.AppDefaults::BETA_KEY_MESSAGE_MAX_LENGTH])]
    public string $message = '';

    public bool $showCreateForm = false;

    protected function queryString(): array
    {
        return [
            'sortBy' => ['except' => 'created_at'],
            'sortDirection' => ['except' => 'desc'],
        ];
    }

    public function mount(): void
    {
        $this->authorize('admin');
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
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
            startBalance: $this->startBalance,
            message: $this->message !== '' ? $this->message : null,
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
        $this->startBalance = null;
        $this->message = '';
        $this->showCreateForm = false;
    }

    public function render(
        BetaAccessKeyRepositoryInterface $betaKeys,
        OrganisationRepositoryInterface $organisations,
    ): View {
        return view('pages.admin.beta-keys', [
            'keys' => $betaKeys->paginate(
                sortBy: $this->sortBy,
                sortDirection: $this->sortDirection,
            ),
            'organisations' => $organisations->findAll(),
        ]);
    }
}
