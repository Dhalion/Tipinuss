<?php

declare(strict_types=1);

namespace App\Livewire\Page;

use App\Actions\Auth\RegisterUserAction;
use App\Constants\AppDefaults;
use App\DTOs\Auth\RegisterData;
use App\Exceptions\InvalidBetaKeyException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;
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

    #[Validate(['nullable', 'string', 'max:'.AppDefaults::BETA_KEY_MAX_LENGTH])]
    public string $betaKey = '';

    public function mount(): void
    {
        $key = request()->query('key');

        if ($key !== null && $key !== '') {
            $this->betaKey = trim($key);
            $this->hasBetaKey = true;
        }
    }

    public function register(RegisterUserAction $action): void
    {
        $this->validate();

        try {
            $result = $action->execute(new RegisterData(
                name: $this->name,
                email: $this->email,
                password: $this->password,
                betaKey: $this->hasBetaKey && $this->betaKey !== '' ? trim($this->betaKey) : null,
            ));
        } catch (InvalidBetaKeyException $e) {
            $this->addError('betaKey', $e->getMessage());

            return;
        }

        auth()->login($result->user);
        session()->regenerate();

        if (! $result->user->isApproved()) {
            $this->redirect(route('pending.approval'));

            return;
        }

        if ($result->startBalance !== null || $result->tokenMessage !== null) {
            Session::put('_registration_toast', [
                'balance' => $result->startBalance,
                'message' => $result->tokenMessage,
            ]);
        }

        $this->redirect(route('main'));
    }

    public function render(): View
    {
        return view('pages.register');
    }
}
