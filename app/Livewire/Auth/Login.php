<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public bool $isLoading = false;

    public ?string $errorMessage = null;

    /**
     * Handle the login form submission.
     */
    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $this->isLoading = true;
        $this->errorMessage = null;

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            $this->redirect(route('dashboard'), navigate: true);
        }

        $this->errorMessage = 'Invalid email or password.';
        $this->isLoading = false;
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.app');
    }
}
