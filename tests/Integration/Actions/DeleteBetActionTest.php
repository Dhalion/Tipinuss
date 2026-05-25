<?php

declare(strict_types=1);

namespace Tests\Integration\Actions;

use App\Actions\Betting\DeleteBetAction;
use App\Models\Bet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

final class DeleteBetActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_deletes_bet(): void
    {
        $user = User::factory()->create();
        $bet = Bet::factory()->create(['user_id' => $user->id]);
        $action = app(DeleteBetAction::class);

        $action->execute($bet);

        $this->assertModelMissing($bet);
    }
}
