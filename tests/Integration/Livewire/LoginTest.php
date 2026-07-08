<?php

declare(strict_types=1);

namespace Tests\Integration\Livewire;

use App\Livewire\Page\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;

final class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticates_user_successfully(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        Livewire::test(Login::class)
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(route('main'));
    }

    public function test_authenticate_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        Livewire::test(Login::class)
            ->set('email', 'test@example.com')
            ->set('password', 'wrongpassword')
            ->call('authenticate')
            ->assertHasErrors('email');
    }

    public function test_authenticate_fails_with_missing_fields(): void
    {
        Livewire::test(Login::class)
            ->call('authenticate')
            ->assertHasErrors(['email', 'password']);
    }

    public function test_authenticate_fails_with_invalid_email(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'not-an-email')
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertHasErrors('email');
    }

    public function test_redirects_to_pending_approval_in_restricted_mode(): void
    {
        config(['app.restricted_mode' => true]);

        $user = User::factory()->unapproved()->create([
            'email' => 'pending@example.com',
            'password' => 'password123',
        ]);

        Livewire::test(Login::class)
            ->set('email', 'pending@example.com')
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(route('pending.approval'));
    }
}
