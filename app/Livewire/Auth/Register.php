<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

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

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        session()->flash('success', 'Account created! Please verify your email.');

        $this->redirect(route('dashboard'), navigate: true);

        $this->isLoading = false;
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.app');
    }
}
