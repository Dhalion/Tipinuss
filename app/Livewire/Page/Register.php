<?php

declare(strict_types=1);

namespace App\Livewire\Page;

use App\Actions\Auth\RegisterUserAction;
use App\DTOs\Auth\RegisterData;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class Register extends Component
{
    #[Validate('required|min:3|max:100')]
    public string $name = '';

    #[Validate('required|email|unique:users')]
    public string $email = '';

    #[Validate('required|min:8|confirmed')]
    public string $password = '';

    #[Validate('required')]
    public string $password_confirmation = '';

    public function register(RegisterUserAction $action): void
    {
        $this->validate();

        $user = $action->execute(RegisterData::make(
            name: $this->name,
            email: $this->email,
            password: $this->password,
        ));

        auth()->login($user);
        session()->regenerate();

        $this->redirect(route('main'));
    }

    public function render(): View
    {
        return view('pages.register');
    }
}
