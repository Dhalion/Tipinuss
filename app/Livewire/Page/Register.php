<?php

declare(strict_types=1);

namespace App\Livewire\Page;

use App\Actions\Auth\RegisterUserAction;
use App\DTOs\Auth\RegisterData;
use App\Exceptions\InvalidBetaKeyException;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class Register extends Component
{
    public bool $hasBetaKey = false;

    #[Validate('required|min:3|max:100')]
    public string $name = '';

    #[Validate('required|email|unique:users')]
    public string $email = '';

    #[Validate('required|min:8|confirmed')]
    public string $password = '';

    #[Validate('required')]
    public string $password_confirmation = '';

    #[Validate('nullable|string|max:32')]
    public string $betaKey = '';

    public function register(RegisterUserAction $action): void
    {
        $this->validate();

        try {
            $user = $action->execute(new RegisterData(
                name: $this->name,
                email: $this->email,
                password: $this->password,
                betaKey: $this->hasBetaKey && $this->betaKey !== '' ? $this->betaKey : null,
            ));
        } catch (InvalidBetaKeyException $e) {
            $this->addError('betaKey', $e->getMessage());

            return;
        }

        auth()->login($user);
        session()->regenerate();

        if (! $user->isApproved()) {
            $this->redirect(route('pending.approval'));

            return;
        }

        $this->redirect(route('main'));
    }

    public function render(): View
    {
        return view('pages.register');
    }
}
