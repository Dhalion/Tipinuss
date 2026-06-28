<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Constants\AppDefaults;
use App\DTOs\Auth\RegisterData;
use App\DTOs\Auth\RegisterResult;
use App\Enums\TransactionType;
use App\Exceptions\InvalidBetaKeyException;
use App\Models\BetaAccessKey;
use App\Models\User;
use App\Repositories\Contracts\BetaAccessKeyRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\User\BalanceTransactionService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class RegisterUserAction
{
    public function __construct(
        private UserRepositoryInterface $users,
        private BetaAccessKeyRepositoryInterface $betaKeys,
        private BalanceTransactionService $balanceTransactions,
    ) {}

    public function execute(RegisterData $data): RegisterResult
    {
        $betaMode = (bool) config('app.beta_mode', false);
        /** @var BetaAccessKey|null $betaKeyModel */
        $betaKeyModel = null;

        $user = new User([
            'name' => $data->name,
            'email' => $data->email,
        ]);

        $user->is_approved = true;

        if ($betaMode && $data->betaKey === null) {
            $user->is_approved = false;
        }

        if ($data->betaKey !== null) {
            $betaKeyModel = $this->betaKeys->findByKey($data->betaKey);

            if ($betaKeyModel === null) {
                throw InvalidBetaKeyException::notFound($data->betaKey);
            }

            if (! $betaKeyModel->isValid()) {
                if ($betaKeyModel->used_at !== null) {
                    throw InvalidBetaKeyException::alreadyUsed();
                }

                if ($betaKeyModel->expires_at !== null && $betaKeyModel->expires_at->isPast()) {
                    throw InvalidBetaKeyException::expired();
                }

                throw InvalidBetaKeyException::inactive();
            }

            $user->is_approved = true;
            $user->organisation_id = $betaKeyModel->organisation_id;
        }

        $user->password = $data->password;

        return DB::transaction(function () use ($user, $betaKeyModel): RegisterResult {
            $startBalance = $betaKeyModel?->start_balance ?? AppDefaults::START_BALANCE;
            $user->soapnuts = $startBalance;

            $user = $this->users->save($user);

            $this->balanceTransactions->log(
                user: $user,
                type: TransactionType::Initial,
                amount: $startBalance,
                balanceAfter: $startBalance,
                description: 'Initial balance from registration',
            );

            if ($betaKeyModel !== null) {
                $betaKeyModel->used_at = Carbon::now();
                $betaKeyModel->used_by_user_id = $user->id;
                $this->betaKeys->save($betaKeyModel);
            }

            return new RegisterResult(
                user: $user,
                startBalance: $betaKeyModel?->start_balance,
                tokenMessage: $betaKeyModel?->message,
            );
        });
    }
}
