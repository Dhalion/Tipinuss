<?php

declare(strict_types=1);

namespace App\Livewire\Page;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|min:8')]
    public string $password = '';

    public function authenticate(): void
    {
        $this->validate();

        $throttleKey = Str::lower($this->email).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, maxAttempts: 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]));

            return;
        }

        if (auth()->attempt(['email' => $this->email, 'password' => $this->password])) {
            RateLimiter::clear($throttleKey);
            session()->regenerate();
            $this->redirect(route('main'));
        } else {
            RateLimiter::hit($throttleKey, decaySeconds: 60);
            $this->addError('email', trans('auth.invalid_credentials'));
        }
    }

    public function render(): View
    {
        return view('pages.login');
    }
}
