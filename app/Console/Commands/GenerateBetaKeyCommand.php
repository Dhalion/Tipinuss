<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Auth\CreateBetaAccessKeyAction;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

final class GenerateBetaKeyCommand extends Command
{
    protected $signature = 'beta:generate-key
        {--organisation-id= : UUID der Organisation}
        {--key= : Optionaler benutzerdefinierter Key}
        {--expires-at= : Ablaufdatum (z.B. 2026-12-31)}
        {--start-balance= : Start-Guthaben für den sich registrierenden User}
        {--admin-email= : Admin-Email für created_by (default: erster Admin)}';

    protected $description = 'Erzeugt einen neuen Beta-Zugangsschlüssel';

    public function handle(
        CreateBetaAccessKeyAction $action,
        UserRepositoryInterface $users,
    ): int {
        $organisationId = $this->option('organisation-id');

        if ($organisationId === null) {
            $this->error('--organisation-id ist erforderlich.');

            return self::FAILURE;
        }

        $admin = $this->resolveAdmin($users);
        if ($admin === null) {
            return self::FAILURE;
        }

        Auth::setUser($admin);

        $startBalance = $this->option('start-balance');
        if ($startBalance !== null) {
            if (! is_string($startBalance) || ! ctype_digit($startBalance)) {
                $this->error('--start-balance muss eine positive ganze Zahl sein.');

                return self::FAILURE;
            }
        }

        $betaKey = $action->execute(
            admin: $admin,
            organisationId: $organisationId,
            customKey: $this->option('key') ?: null,
            expiresAt: $this->option('expires-at'),
            startBalance: $startBalance !== null ? (int) $startBalance : null,
        );

        $this->table(
            ['Key', 'Organisation', 'Start-Balance', 'Läuft ab'],
            [[
                $betaKey->key,
                $betaKey->organisation?->name ?? $organisationId,
                $betaKey->start_balance !== null ? number_format($betaKey->start_balance, 0, ',', '.').' 🌰' : '—',
                $betaKey->expires_at?->format('d.m.Y') ?? '—',
            ]],
        );

        return self::SUCCESS;
    }

    private function resolveAdmin(UserRepositoryInterface $users): ?User
    {
        $email = $this->option('admin-email');

        if ($email !== null) {
            $user = $users->findByEmail($email);
            if ($user === null) {
                $this->error("Admin mit E-Mail „{$email}“ nicht gefunden.");

                return null;
            }

            return $user;
        }

        $admin = $users->findFirstAdmin();
        if ($admin === null) {
            $this->error('Es existiert kein Admin-Benutzer. Bitte --admin-email angeben.');

            return null;
        }

        return $admin;
    }
}
