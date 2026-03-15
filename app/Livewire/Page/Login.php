<?php

namespace App\Livewire\Page;

use Livewire\Attributes\Validate;
use Livewire\Component;

class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|min:6')]
    public string $password = '';

    public function authenticate(): void
    {
        $this->validate();

        if (auth()->attempt(['email' => $this->email, 'password' => $this->password])) {
            $this->redirect(route('main'));
        } else {
            $this->addError('email', trans('auth.invalid_credentials'));
        }
    }

    public function render()
    {
        return view('pages.login');
    }
}
