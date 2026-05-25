<?php

declare(strict_types=1);

namespace Tests\Integration\Actions\Admin;

use App\Actions\Admin\ApproveUserAction;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

final class ApproveUserActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_approves_user(): void
    {
        $user = User::factory()->unapproved()->create();
        $action = app(ApproveUserAction::class);

        $action->execute($user);

        $this->assertTrue($user->fresh()->is_approved);
    }

    public function test_approves_user_with_organisation(): void
    {
        $organisation = Organisation::factory()->create();
        $user = User::factory()->unapproved()->create();
        $action = app(ApproveUserAction::class);

        $action->execute($user, $organisation->id);

        $this->assertTrue($user->fresh()->is_approved);
        $this->assertSame($organisation->id, $user->fresh()->organisation_id);
    }
}
