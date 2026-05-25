<?php

declare(strict_types=1);

namespace Tests\Integration\Livewire;

use App\Livewire\Page\Register;
use App\Models\BetaAccessKey;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;

final class RegisterComponentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.beta_mode' => false]);
    }

    public function test_registers_user_successfully(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'New User')
            ->set('email', 'new@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('main'));

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'new@example.com',
        ]);
    }

    public function test_registers_user_with_beta_key(): void
    {
        $organisation = Organisation::factory()->create();
        $admin = User::factory()->admin()->create();
        $betaKey = BetaAccessKey::factory()->create([
            'organisation_id' => $organisation->id,
            'created_by_user_id' => $admin->id,
        ]);

        Livewire::test(Register::class)
            ->set('name', 'Beta User')
            ->set('email', 'beta@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->set('hasBetaKey', true)
            ->set('betaKey', $betaKey->key)
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('main'));

        $this->assertDatabaseHas('users', [
            'email' => 'beta@example.com',
            'organisation_id' => $organisation->id,
        ]);
    }

    public function test_registration_fails_with_invalid_beta_key(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'Bad Beta')
            ->set('email', 'badbeta@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->set('hasBetaKey', true)
            ->set('betaKey', 'INVALID-KEY')
            ->call('register')
            ->assertHasErrors('betaKey');
    }

    public function test_validation_fails_for_missing_fields(): void
    {
        Livewire::test(Register::class)
            ->call('register')
            ->assertHasErrors(['name', 'email', 'password']);
    }

    public function test_validation_fails_for_password_mismatch(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'Test')
            ->set('email', 'test@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'different')
            ->call('register')
            ->assertHasErrors(['password']);
    }

    public function test_redirects_to_pending_approval_in_beta_mode(): void
    {
        config(['app.beta_mode' => true]);

        Livewire::test(Register::class)
            ->set('name', 'Pending User')
            ->set('email', 'pending@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('pending.approval'));
    }
}
