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
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class UserManagement extends Component
{
    public string $approvalFilter = '';

    /** @var array<string, int> */
    public array $balanceAdjustments = [];

    public function mount(): void
    {
        $this->authorize('admin');
    }

    public function setFilter(string $filter): void
    {
        $this->approvalFilter = $filter;
    }

    public function adjustBalance(
        string $userId,
        AdjustUserBalanceAction $action,
        UserRepositoryInterface $users,
    ): void {
        $adjustment = $this->balanceAdjustments[$userId] ?? 0;
        if ($adjustment === 0) {
            return;
        }

        $target = $users->findById($userId);
        if ($target === null) {
            return;
        }

        $action->execute($target, (int) $adjustment);
        $this->balanceAdjustments[$userId] = 0;
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
            'users' => $users->allWithBetCountByApprovalStatus($isApproved),
            'organisations' => $organisations->findAll(),
            'pendingCount' => $users->pendingCount(),
        ]);
    }
}
