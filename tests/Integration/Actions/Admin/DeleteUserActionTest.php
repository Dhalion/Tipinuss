<?php

declare(strict_types=1);

namespace Tests\Integration\Actions\Admin;

use App\Actions\Admin\DeleteUserAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

final class DeleteUserActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_deletes_user(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();

        $action = app(DeleteUserAction::class);

        $action->execute($admin, $target);

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_throws_when_deleting_self(): void
    {
        $admin = User::factory()->admin()->create();
        $action = app(DeleteUserAction::class);

        $this->expectExceptionMessage(__('admin.users.cannot_delete_self'));

        $action->execute($admin, $admin);
    }
}
