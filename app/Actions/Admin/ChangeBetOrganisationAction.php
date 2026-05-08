<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Models\Bet;
use App\Models\Organisation;
use App\Repositories\Contracts\BetRepositoryInterface;

final class ChangeBetOrganisationAction
{
    public function __construct(private BetRepositoryInterface $bets) {}

    public function execute(Bet $bet, ?Organisation $organisation): void
    {
        $bet->organisation_id = $organisation?->id;
        $this->bets->save($bet);
    }
}
