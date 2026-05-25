<?php

declare(strict_types=1);

namespace Tests\Integration\Actions\Admin;

use App\Actions\Auth\CreateBetaAccessKeyAction;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

final class CreateBetaAccessKeyActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_beta_access_key_with_auto_generated_key(): void
    {
        $admin = User::factory()->admin()->create();
        $organisation = Organisation::factory()->create();
        $action = app(CreateBetaAccessKeyAction::class);

        $betaKey = $action->execute(
            admin: $admin,
            organisationId: $organisation->id,
            customKey: null,
            expiresAt: null,
            startBalance: null,
        );

        $this->assertNotNull($betaKey->id);
        $this->assertSame($organisation->id, $betaKey->organisation_id);
        $this->assertNotNull($betaKey->key);
        $this->assertStringStartsWith('BETA-', $betaKey->key);
        $this->assertTrue($betaKey->fresh()->is_active);
    }

    public function test_creates_beta_access_key_with_custom_key(): void
    {
        $admin = User::factory()->admin()->create();
        $organisation = Organisation::factory()->create();
        $action = app(CreateBetaAccessKeyAction::class);

        $betaKey = $action->execute(
            admin: $admin,
            organisationId: $organisation->id,
            customKey: 'CUSTOM-KEY-123',
            expiresAt: null,
            startBalance: 500,
        );

        $this->assertSame('CUSTOM-KEY-123', $betaKey->key);
        $this->assertSame(500, $betaKey->start_balance);
    }
}
