<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\BetaAccessKey;
use App\Models\User;
use App\Repositories\Contracts\BetaAccessKeyRepositoryInterface;
use Illuminate\Support\Str;

final class CreateBetaAccessKeyAction
{
    public function __construct(
        private BetaAccessKeyRepositoryInterface $betaKeys,
    ) {}

    public function execute(
        User $admin,
        string $organisationId,
        ?string $customKey = null,
        ?string $expiresAt = null,
        ?int $startBalance = null,
    ): BetaAccessKey {
        $key = $customKey ?? $this->generateKey();

        $betaKey = new BetaAccessKey([
            'key' => $key,
            'organisation_id' => $organisationId,
            'created_by_user_id' => $admin->id,
            'expires_at' => $expiresAt,
            'start_balance' => $startBalance,
        ]);

        return $this->betaKeys->save($betaKey);
    }

    private function generateKey(): string
    {
        do {
            $key = 'BETA-'.strtoupper(Str::random(8));
        } while ($this->betaKeys->existsByKey($key));

        return $key;
    }
}
