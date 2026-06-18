<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $email = '';

    public bool $isLoading = false;

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    /**
     * Send the password reset link.
     */
    public function sendResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
        ]);

        $this->isLoading = true;
        $this->errorMessage = null;
        $this->successMessage = null;

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->successMessage = 'If that email is registered, you will receive a reset link.';
        } else {
            // Don't reveal whether the email exists
            $this->successMessage = 'If that email is registered, you will receive a reset link.';
        }

        $this->isLoading = false;
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.auth.forgot-password')
            ->layout('layouts.app');
    }
}
