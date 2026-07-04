<?php

declare(strict_types=1);

namespace App\Actions\Betting;

use App\DTOs\Betting\DeleteBetData;
use App\Enums\TransactionType;
use App\Repositories\Contracts\BetOptionRepositoryInterface;
use App\Repositories\Contracts\BetRepositoryInterface;
use App\Repositories\Contracts\UserBetRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\User\BalanceTransactionService;
use App\Services\User\UserBalanceService;
use Illuminate\Support\Facades\DB;

final readonly class DeleteBetAction
{
    public function __construct(
        private BetRepositoryInterface $bets,
        private BetOptionRepositoryInterface $betOptionRepository,
        private UserBetRepositoryInterface $userBets,
        private UserRepositoryInterface $users,
        private UserBalanceService $balance,
        private BalanceTransactionService $balanceTransactions,
    ) {}

    public function execute(DeleteBetData $data): void
    {
        DB::transaction(function () use ($data): void {
            if ($data->refund) {
                $betOptionIds = $this->betOptionRepository->getOptionIdsByBetId($data->bet->id);

                if ($betOptionIds->isNotEmpty()) {
                    /** @var list<string> $optionIdList */
                    $optionIdList = $betOptionIds->values()->toArray();
                    $placedBets = $this->userBets->findByOptionIds($optionIdList);

                    $refunds = [];

                    foreach ($placedBets as $placedBet) {
                        $uid = $placedBet->user_id;
                        $refunds[$uid] = ($refunds[$uid] ?? 0) + $placedBet->amount_wagered;
                    }

                    if ($refunds !== []) {
                        $users = $this->users->findByIds(array_keys($refunds));

                        foreach ($refunds as $userId => $totalWagered) {
                            $user = $users->get($userId);

                            if ($user === null) {
                                continue;
                            }

                            $balanceBefore = $user->soapnuts;
                            $this->balance->incrementBalance($user, $totalWagered);

                            $this->balanceTransactions->log(
                                user: $user,
                                type: TransactionType::BetRefund,
                                amount: $totalWagered,
                                balanceAfter: $balanceBefore + $totalWagered,
                                description: $data->bet->title,
                            );
                        }
                    }
                }
            }

            $this->bets->delete($data->bet);
        });
    }
}
