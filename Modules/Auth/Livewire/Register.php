<?php

declare(strict_types=1);

namespace Modules\Auth\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Modules\Auth\Services\AuthService;

class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $isLoading = false;

    public ?string $errorMessage = null;

    /**
     * Handle the registration form submission.
     */
    public function register(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $this->isLoading = true;
        $this->errorMessage = null;

        try {
            /** @var AuthService $service */
            $service = app(AuthService::class);
            $user = $service->registerWeb([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
            ]);

            Auth::login($user);

            session()->flash('success', 'Account created! Please verify your email.');

            $this->redirect(route('dashboard'), navigate: true);
        } catch (\Exception $e) {
            $this->errorMessage = 'Registration failed. Please try again.';
        }

        $this->isLoading = false;
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('auth::auth.register');
    }
}
