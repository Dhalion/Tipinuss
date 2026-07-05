<?php

declare(strict_types=1);

namespace App\Livewire\Page;

use App\Constants\AppDefaults;
use App\Repositories\Contracts\BalanceTransactionRepositoryInterface;
use App\Repositories\Contracts\UserBetRepositoryInterface;
use App\Services\User\TransactionHistoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class Account extends Component
{
    #[Computed]
    public function totalBetsCount(UserBetRepositoryInterface $userBets): int
    {
        $user = Auth::user();

        return $user !== null ? $userBets->countForUser($user) : 0;
    }

    #[Computed]
    public function wonBetsCount(UserBetRepositoryInterface $userBets): int
    {
        $user = Auth::user();

        return $user !== null ? $userBets->countForUserByStatus($user, 'won') : 0;
    }

    #[Computed]
    public function lostBetsCount(UserBetRepositoryInterface $userBets): int
    {
        $user = Auth::user();

        return $user !== null ? $userBets->countForUserByStatus($user, 'lost') : 0;
    }

    public function render(
        UserBetRepositoryInterface $userBets,
        TransactionHistoryService $history,
        BalanceTransactionRepositoryInterface $transactions,
    ): View {
        $user = Auth::user();

        if ($user === null) {
            return view('pages.account', [
                'userBets' => collect(),
                'historyEntries' => [],
                'chartDataJson' => '[]',
            ]);
        }

        $chartData = $transactions->chartDataForUser($user, limit: AppDefaults::CHART_DATA_LIMIT);

        $chartDataJson = $chartData->map(fn ($transaction) => [
            'x' => $transaction->created_at->format('Y-m-d\TH:i:s'),
            'y' => $transaction->balance_after,
        ])->toJson();

        return view('pages.account', [
            'userBets' => $userBets->recentForUser($user),
            'historyEntries' => $history->forUser($user, limit: AppDefaults::HISTORY_LIMIT),
            'chartDataJson' => $chartDataJson,
            'totalBetsCount' => $this->totalBetsCount($userBets),
            'wonBetsCount' => $this->wonBetsCount($userBets),
            'lostBetsCount' => $this->lostBetsCount($userBets),
        ]);
    }
}
