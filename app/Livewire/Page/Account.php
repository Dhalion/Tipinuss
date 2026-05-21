<?php

declare(strict_types=1);

namespace App\Livewire\Page;

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
    public function totalBetsCount(): int
    {
        $user = Auth::user();

        return $user !== null ? $user->userBets()->count() : 0;
    }

    #[Computed]
    public function wonBetsCount(): int
    {
        $user = Auth::user();

        return $user !== null ? $user->userBets()->where('status', 'won')->count() : 0;
    }

    #[Computed]
    public function lostBetsCount(): int
    {
        $user = Auth::user();

        return $user !== null ? $user->userBets()->where('status', 'lost')->count() : 0;
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

        $chartData = $transactions->chartDataForUser($user, limit: 100);

        $chartDataJson = $chartData->map(fn ($transaction) => [
            'x' => $transaction->created_at->format('Y-m-d\TH:i:s'),
            'y' => $transaction->balance_after,
        ])->toJson();

        return view('pages.account', [
            'userBets' => $userBets->recentForUser($user),
            'historyEntries' => $history->forUser($user, limit: 20),
            'chartDataJson' => $chartDataJson,
            'totalBetsCount' => $this->totalBetsCount(),
            'wonBetsCount' => $this->wonBetsCount(),
            'lostBetsCount' => $this->lostBetsCount(),
        ]);
    }
}
