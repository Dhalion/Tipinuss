<?php

declare(strict_types=1);

namespace App\Actions\Betting;

use App\DTOs\Betting\CreateBetData;
use App\Enums\BetStatus;
use App\Models\Bet;
use App\Repositories\Contracts\BetOptionRepositoryInterface;
use App\Repositories\Contracts\BetRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CreateBetAction
{
    public function __construct(
        private BetRepositoryInterface $bets,
        private BetOptionRepositoryInterface $betOptions,
    ) {}

    public function execute(CreateBetData $data): Bet
    {
        return DB::transaction(function () use ($data): Bet {
            $bet = new Bet([
                'user_id' => $data->creator->id,
                'organisation_id' => $data->organisationId,
                'title' => $data->title,
                'slug' => $this->generateSlug($data->title),
                'description' => $data->description,
                'status' => BetStatus::Open,
                'expires_at' => $data->expiresAt,
            ]);

            $this->bets->save($bet);

            foreach ($data->options as $optionData) {
                $this->betOptions->createForBet($bet, $optionData);
            }

            return $bet;
        });
    }

    private function generateSlug(string $title, int $maxAttempts = 100): string
    {
        $base = Str::slug($title);

        for ($i = 0; $i < $maxAttempts; $i++) {
            $slug = $i === 0 ? $base : $base.'-'.$i;

            if (! $this->bets->existsBySlug($slug)) {
                return $slug;
            }
        }

        return $base.'-'.Str::random(6);
    }
}
