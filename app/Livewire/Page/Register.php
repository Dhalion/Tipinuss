<?php

namespace App\Livewire\Page;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Register extends Component
{
    #[Validate('required|min:3')]
    public string $name = '';

    #[Validate('required|email|unique:users')]
    public string $email = '';

    #[Validate('required|min:6|confirmed')]
    public string $password = '';

    #[Validate('required')]
    public string $password_confirmation = '';

    public function register(): void
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
        ]);

        auth()->login($user);

        $this->redirect(route('main'));
    }

    public function render()
    {
        return view('pages.register');
    }
}
