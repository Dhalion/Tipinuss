<?php

declare(strict_types=1);

namespace Tests\Integration\Actions;

use App\Actions\Auth\RegisterUserAction;
use App\Constants\AppDefaults;
use App\DTOs\Auth\RegisterData;
use App\DTOs\Auth\RegisterResult;
use App\Enums\TransactionType;
use App\Exceptions\InvalidBetaKeyException;
use App\Models\BetaAccessKey;
use App\Models\Organisation;
use App\Models\User;
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

        $result = $action->execute(new RegisterData(
            name: 'New User',
            email: 'new@example.com',
            password: 'secret123',
        ));

        $this->assertInstanceOf(RegisterResult::class, $result);
        $this->assertSame('New User', $result->user->name);
        $this->assertSame('new@example.com', $result->user->email);
        $this->assertTrue($result->user->fresh()->is_approved);
        $this->assertSame(AppDefaults::START_BALANCE, $result->user->fresh()->soapnuts);
        $this->assertNull($result->user->organisation_id);
        $this->assertNull($result->startBalance);
        $this->assertNull($result->tokenMessage);

        $this->assertDatabaseHas('balance_transactions', [
            'user_id' => $result->user->id,
            'type' => TransactionType::Initial,
            'amount' => AppDefaults::START_BALANCE,
        ]);
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

        $result = $action->execute(new RegisterData(
            name: 'Beta User',
            email: 'beta@example.com',
            password: 'secret123',
            betaKey: $betaKey->key,
        ));

        $this->assertTrue($result->user->is_approved);
        $this->assertSame($organisation->id, $result->user->organisation_id);
        $this->assertSame(500, $result->user->soapnuts);
        $this->assertSame(500, $result->startBalance);
        $this->assertNull($result->tokenMessage);

        $betaKey->refresh();
        $this->assertNotNull($betaKey->used_at);
        $this->assertSame($result->user->id, $betaKey->used_by_user_id);

        $this->assertDatabaseHas('balance_transactions', [
            'user_id' => $result->user->id,
            'type' => TransactionType::Initial,
            'amount' => 500,
        ]);
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
        $betaKey->expires_at = now()->subDay();
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
        $betaKey->expires_at = now()->addYear();
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

        $result = $action->execute(new RegisterData(
            name: 'Pending User',
            email: 'pending@example.com',
            password: 'secret123',
        ));

        $this->assertInstanceOf(RegisterResult::class, $result);
        $this->assertFalse($result->user->is_approved);
    }

    public function test_registers_user_with_token_message(): void
    {
        $organisation = Organisation::factory()->create();
        $admin = User::factory()->admin()->create();
        $betaKey = BetaAccessKey::factory()->create([
            'organisation_id' => $organisation->id,
            'created_by_user_id' => $admin->id,
            'start_balance' => 500,
            'message' => 'Herzlich willkommen!',
        ]);

        $action = app(RegisterUserAction::class);

        $result = $action->execute(new RegisterData(
            name: 'Message User',
            email: 'msg@example.com',
            password: 'secret123',
            betaKey: $betaKey->key,
        ));

        $this->assertSame(500, $result->startBalance);
        $this->assertSame('Herzlich willkommen!', $result->tokenMessage);
    }

    public function test_registers_user_without_start_balance_uses_default(): void
    {
        $organisation = Organisation::factory()->create();
        $admin = User::factory()->admin()->create();
        $betaKey = BetaAccessKey::factory()->create([
            'organisation_id' => $organisation->id,
            'created_by_user_id' => $admin->id,
            'start_balance' => null,
        ]);

        $action = app(RegisterUserAction::class);

        $result = $action->execute(new RegisterData(
            name: 'Default Balance',
            email: 'default@example.com',
            password: 'secret123',
            betaKey: $betaKey->key,
        ));

        $this->assertSame(AppDefaults::START_BALANCE, $result->user->fresh()->soapnuts);
        $this->assertNull($result->startBalance);
        $this->assertNull($result->tokenMessage);

        $this->assertDatabaseHas('balance_transactions', [
            'user_id' => $result->user->id,
            'type' => TransactionType::Initial,
            'amount' => AppDefaults::START_BALANCE,
        ]);
    }

    public function test_logs_initial_balance_transaction_without_beta_key(): void
    {
        $action = app(RegisterUserAction::class);

        $result = $action->execute(new RegisterData(
            name: 'No Key User',
            email: 'nokey@example.com',
            password: 'secret123',
        ));

        $this->assertDatabaseHas('balance_transactions', [
            'user_id' => $result->user->id,
            'type' => TransactionType::Initial,
            'amount' => AppDefaults::START_BALANCE,
            'balance_after' => AppDefaults::START_BALANCE,
        ]);
    }
}
