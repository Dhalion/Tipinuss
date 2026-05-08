<?php

declare(strict_types=1);

namespace App\Actions\Betting;

use App\DTOs\Betting\CreateBetData;
use App\Enums\BetStatus;
use App\Models\Bet;
use App\Repositories\Contracts\BetOptionRepositoryInterface;
use App\Repositories\Contracts\BetRepositoryInterface;
use Illuminate\Support\Facades\DB;

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
                'description' => $data->description,
                'status' => BetStatus::Open,
                'expires_at' => $data->expiresAt,
            ]);

            $this->bets->save($bet);

            foreach ($data->options as $optionData) {
                $this->betOptions->createForBet($bet, [
                    'title' => $optionData->title,
                    'odds' => $optionData->odds,
                    'base_odds' => $optionData->odds,
                ]);
            }

            return $bet;
        });
    }
}
