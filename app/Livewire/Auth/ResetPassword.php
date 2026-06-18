<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ResetPassword extends Component
{
    public string $email = '';

    public string $token = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $isLoading = false;

    public ?string $errorMessage = null;

    /**
     * Initialize component with route parameters.
     */
    public function mount(string $token, string $email = ''): void
    {
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Handle the password reset submission.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $this->isLoading = true;
        $this->errorMessage = null;

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('success', 'Password reset successfully. You can now log in.');
            $this->redirect(route('login'), navigate: true);
        }

        $this->errorMessage = 'Invalid or expired reset token.';
        $this->isLoading = false;
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.auth.reset-password')
            ->layout('layouts.app');
    }
}
