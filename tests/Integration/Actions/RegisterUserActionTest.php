<?php

declare(strict_types=1);

namespace Tests\Integration\Actions;

use App\Actions\Auth\RegisterUserAction;
use App\DTOs\Auth\RegisterData;
use App\Exceptions\InvalidBetaKeyException;
use App\Models\BetaAccessKey;
use App\Models\Organisation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

final class RegisterUserActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.beta_mode' => false]);
    }

    public function test_registers_user_without_beta_key(): void
    {
        $action = app(RegisterUserAction::class);

        $user = $action->execute(new RegisterData(
            name: 'New User',
            email: 'new@example.com',
            password: 'secret123',
        ));

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('New User', $user->name);
        $this->assertSame('new@example.com', $user->email);
        $this->assertTrue($user->fresh()->is_approved);
        $this->assertSame(1000, $user->fresh()->soapnuts);
        $this->assertNull($user->organisation_id);
    }

    public function test_registers_user_with_valid_beta_key(): void
    {
        $organisation = Organisation::factory()->create();
        $admin = User::factory()->admin()->create();
        $betaKey = BetaAccessKey::factory()
            ->withStartBalance(500)
            ->create([
                'organisation_id' => $organisation->id,
                'created_by_user_id' => $admin->id,
            ]);

        $action = app(RegisterUserAction::class);

        $user = $action->execute(new RegisterData(
            name: 'Beta User',
            email: 'beta@example.com',
            password: 'secret123',
            betaKey: $betaKey->key,
        ));

        $this->assertTrue($user->is_approved);
        $this->assertSame($organisation->id, $user->organisation_id);
        $this->assertSame(500, $user->soapnuts);

        $betaKey->refresh();
        $this->assertNotNull($betaKey->used_at);
        $this->assertSame($user->id, $betaKey->used_by_user_id);
    }

    public function test_throws_when_beta_key_not_found(): void
    {
        $action = app(RegisterUserAction::class);

        $this->expectException(InvalidBetaKeyException::class);
        $this->expectExceptionMessage(__('auth.beta_key_invalid', ['key' => 'NONEXISTENT']));

        $action->execute(new RegisterData(
            name: 'Test',
            email: 'test@example.com',
            password: 'secret123',
            betaKey: 'NONEXISTENT',
        ));
    }

    public function test_throws_when_beta_key_already_used(): void
    {
        $organisation = Organisation::factory()->create();
        $admin = User::factory()->admin()->create();
        $otherUser = User::factory()->create();
        $betaKey = BetaAccessKey::factory()->used()->create([
            'organisation_id' => $organisation->id,
            'created_by_user_id' => $admin->id,
            'used_by_user_id' => $otherUser->id,
        ]);

        $action = app(RegisterUserAction::class);

        $this->expectException(InvalidBetaKeyException::class);
        $this->expectExceptionMessage(__('auth.beta_key_already_used'));

        $action->execute(new RegisterData(
            name: 'Test',
            email: 'test@example.com',
            password: 'secret123',
            betaKey: $betaKey->key,
        ));
    }

    public function test_throws_when_beta_key_expired(): void
    {
        $organisation = Organisation::factory()->create();
        $admin = User::factory()->admin()->create();

        $betaKey = new BetaAccessKey;
        $betaKey->key = 'EXPIRED-KEY-123';
        $betaKey->is_active = true;
        $betaKey->organisation_id = $organisation->id;
        $betaKey->created_by_user_id = $admin->id;
        $betaKey->expires_at = Carbon::now()->subDay();
        $betaKey->save();
        $betaKey->refresh();

        $action = app(RegisterUserAction::class);

        $this->expectException(InvalidBetaKeyException::class);
        $this->expectExceptionMessage(__('auth.beta_key_expired'));

        $action->execute(new RegisterData(
            name: 'Test',
            email: 'test@example.com',
            password: 'secret123',
            betaKey: $betaKey->key,
        ));
    }

    public function test_throws_when_beta_key_inactive(): void
    {
        $organisation = Organisation::factory()->create();
        $admin = User::factory()->admin()->create();
        $betaKey = new BetaAccessKey;
        $betaKey->key = 'INACTIVE-KEY-456';
        $betaKey->is_active = false;
        $betaKey->organisation_id = $organisation->id;
        $betaKey->created_by_user_id = $admin->id;
        $betaKey->expires_at = Carbon::now()->addYear();
        $betaKey->save();

        $action = app(RegisterUserAction::class);

        $this->expectException(InvalidBetaKeyException::class);
        $this->expectExceptionMessage(__('auth.beta_key_inactive'));

        $action->execute(new RegisterData(
            name: 'Test',
            email: 'test@example.com',
            password: 'secret123',
            betaKey: $betaKey->key,
        ));
    }

    public function test_sets_not_approved_when_no_key_and_beta_mode(): void
    {
        config(['app.beta_mode' => true]);
        $action = app(RegisterUserAction::class);

        $user = $action->execute(new RegisterData(
            name: 'Pending User',
            email: 'pending@example.com',
            password: 'secret123',
        ));

        $this->assertFalse($user->is_approved);
    }
}
