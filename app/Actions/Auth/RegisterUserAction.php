<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\RegisterData;
use App\Exceptions\InvalidBetaKeyException;
use App\Models\BetaAccessKey;
use App\Models\User;
use App\Repositories\Contracts\BetaAccessKeyRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class RegisterUserAction
{
    public function __construct(
        private UserRepositoryInterface $users,
        private BetaAccessKeyRepositoryInterface $betaKeys,
    ) {}

    public function execute(RegisterData $data): User
    {
        $betaMode = (bool) config('app.beta_mode', false);
        /** @var BetaAccessKey|null $betaKeyModel */
        $betaKeyModel = null;

        $user = new User([
            'name' => $data->name,
            'email' => $data->email,
        ]);

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

                if ($betaKeyModel->expires_at !== null && $betaKeyModel->expires_at instanceof Carbon && $betaKeyModel->expires_at->isPast()) {
                    throw InvalidBetaKeyException::expired();
                }

                throw InvalidBetaKeyException::inactive();
            }

            $user->is_approved = true;
            $user->organisation_id = $betaKeyModel->organisation_id;
        }

        $user->password = Hash::make($data->password);

        return DB::transaction(function () use ($user, $betaKeyModel): User {
            if ($betaKeyModel !== null && $betaKeyModel->start_balance !== null) {
                $user->soapnuts = $betaKeyModel->start_balance;
            }

            $user = $this->users->save($user);

            if ($betaKeyModel !== null) {
                $betaKeyModel->used_at = Carbon::now();
                $betaKeyModel->used_by_user_id = $user->id;
                $this->betaKeys->save($betaKeyModel);
            }

            return $user;
        });
    }
}
