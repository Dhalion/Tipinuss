<?php

declare(strict_types=1);

namespace App\Livewire\Page\Admin;

use App\Actions\Admin\AdjustUserBalanceAction;
use App\Actions\Admin\ApproveUserAction;
use App\Actions\Admin\DeleteUserAction;
use App\Actions\Admin\ToggleUserAdminAction;
use App\Exceptions\BetException;
use App\Models\User;
use App\Repositories\Contracts\OrganisationRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

final class UserManagement extends Component
{
    use WithPagination;

    public string $approvalFilter = '';

    public string $sortBy = 'name';

    public string $sortDirection = 'asc';

    public bool $showBalanceModal = false;

    public string $modalUserId = '';

    public int $modalAdjustment = 0;

    protected function queryString(): array
    {
        return [
            'sortBy' => ['except' => 'name'],
            'sortDirection' => ['except' => 'asc'],
        ];
    }

    public function mount(): void
    {
        $this->authorize('admin');
    }

    public function setFilter(string $filter): void
    {
        $this->approvalFilter = $filter;
        $this->resetPage();
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

    public function openBalanceModal(string $userId, UserRepositoryInterface $users): void
    {
        $user = $users->findById($userId);
        if ($user === null) {
            return;
        }

        $this->modalUserId = $userId;
        $this->modalAdjustment = 0;
        $this->showBalanceModal = true;
    }

    public function adjustBalance(AdjustUserBalanceAction $action, UserRepositoryInterface $users): void
    {
        if ($this->modalAdjustment === 0) {
            return;
        }

        $target = $users->findById($this->modalUserId);
        if ($target === null) {
            return;
        }

        $action->execute($target, $this->modalAdjustment);

        $name = $target->name;
        $this->closeBalanceModal();

        Flux::toast(variant: 'success', text: __('admin.balance_modal.success', ['name' => $name]));
    }

    public function closeBalanceModal(): void
    {
        $this->showBalanceModal = false;
        $this->modalUserId = '';
        $this->modalAdjustment = 0;
    }

    public function toggleAdmin(
        string $userId,
        ToggleUserAdminAction $action,
        UserRepositoryInterface $users,
    ): void {
        $admin = auth()->user();
        $target = $users->findById($userId);

        if (! $admin instanceof User || $target === null) {
            return;
        }

        try {
            $action->execute($admin, $target);
        } catch (BetException $e) {
            $this->addError('admin', $e->getMessage());
        }
    }

    public function approveUser(
        string $userId,
        ?string $organisationId,
        ApproveUserAction $action,
        UserRepositoryInterface $users,
    ): void {
        $target = $users->findById($userId);
        if ($target === null) {
            return;
        }

        $action->execute($target, $organisationId);
    }

    public function assignOrganisation(
        string $userId,
        ?string $organisationId,
        OrganisationRepositoryInterface $organisations,
        UserRepositoryInterface $users,
    ): void {
        $target = $users->findById($userId);
        if ($target === null) {
            return;
        }

        $resolvedOrgId = null;
        if ($organisationId !== null && $organisationId !== '') {
            $organisation = $organisations->findById($organisationId);
            if ($organisation === null) {
                return;
            }
            $resolvedOrgId = $organisation->id;
        }

        $target->organisation_id = $resolvedOrgId;
        $users->save($target);
    }

    public function deleteUser(
        string $userId,
        DeleteUserAction $action,
        UserRepositoryInterface $users,
    ): void {
        $admin = auth()->user();
        $target = $users->findById($userId);

        if (! $admin instanceof User || $target === null) {
            return;
        }

        try {
            $action->execute($admin, $target);
        } catch (BetException $e) {
            $this->addError('delete', $e->getMessage());
        }
    }

    public function render(
        UserRepositoryInterface $users,
        OrganisationRepositoryInterface $organisations,
    ): View {
        $isApproved = match ($this->approvalFilter) {
            'approved' => true,
            'pending' => false,
            default => null,
        };

        return view('pages.admin.users', [
            'users' => $users->paginateWithBetCountByApprovalStatus(
                isApproved: $isApproved,
                sortBy: $this->sortBy,
                sortDirection: $this->sortDirection,
            ),
            'organisations' => $organisations->findAll(),
            'pendingCount' => $users->pendingCount(),
        ]);
    }
}
