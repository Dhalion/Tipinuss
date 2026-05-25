<?php

declare(strict_types=1);

namespace Tests\Integration\Actions\Admin;

use App\Actions\Admin\ToggleUserAdminAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

final class ToggleUserAdminActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_toggles_admin_status(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create(['is_admin' => false]);

        $action = app(ToggleUserAdminAction::class);

        $action->execute($admin, $target);

        $this->assertTrue($target->fresh()->is_admin);

        $action->execute($admin, $target);

        $this->assertFalse($target->fresh()->is_admin);
    }

    public function test_throws_when_toggling_self(): void
    {
        $admin = User::factory()->admin()->create();
        $action = app(ToggleUserAdminAction::class);

        $this->expectExceptionMessage(__('admin.users.cannot_toggle_self'));

        $action->execute($admin, $admin);
    }
}
