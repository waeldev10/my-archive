<div>
    <div class="mx-auto max-w-md">
        <h1 class="text-2xl font-bold text-[var(--color-foreground)]">@lang('Log In')</h1>
        <p class="mt-2 text-sm text-[var(--color-foreground-secondary)]">
            @lang('Welcome back. Enter your credentials to access your archives.')
        </p>

        <form wire:submit="login" class="mt-6 space-y-4">
            {{-- Email --}}
            <x-core::input
                label="Email"
                name="email"
                type="email"
                model="email"
                blur
                required
                autofocus
            />

            {{-- Password --}}
            <x-core::input
                label="Password"
                name="password"
                type="password"
                model="password"
                blur
                required
            />

            {{-- Remember & Forgot --}}
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-[var(--color-foreground-secondary)] cursor-pointer">
                    <input type="checkbox" wire:model="remember"
                           class="rounded border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-primary-600)] focus:ring-[var(--color-primary-500)]">
                    @lang('Remember me')
                </label>
                <a href="{{ route('password.request') }}"
                   class="text-sm font-medium text-[var(--color-foreground-secondary)] hover:text-[var(--color-foreground)] no-underline">
                    @lang('Forgot password?')
                </a>
            </div>

            {{-- Error --}}
            @if ($errorMessage)
                <x-core::alert type="danger" :message="$errorMessage" />
            @endif

            {{-- Submit --}}
            <button type="submit" wire:loading.attr="disabled"
                    class="w-full rounded-lg bg-[var(--color-foreground)] px-4 py-2 text-sm font-medium text-[var(--color-foreground-inverse)] hover:opacity-90 disabled:opacity-50 transition-opacity cursor-pointer">
                <span wire:loading.remove wire:target="login">@lang('Log In')</span>
                <span wire:loading wire:target="login">@lang('Logging in...')</span>
            </button>
        </form>

        {{-- Divider --}}
        <div class="relative mt-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-[var(--color-border)]"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="bg-[var(--color-background)] px-2 text-[var(--color-foreground-muted)]">@lang('Or continue with')</span>
            </div>
        </div>

        {{-- Google OAuth --}}
        <div class="mt-4">
            <a href="{{ route('google.redirect') }}"
               class="flex w-full items-center justify-center gap-2 rounded-lg border border-[var(--color-border)] px-4 py-2 text-sm font-medium text-[var(--color-foreground)] hover:bg-[var(--color-surface-secondary)] transition-colors no-underline">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                @lang('Google')
            </a>
        </div>

        <p class="mt-6 text-center text-sm text-[var(--color-foreground-secondary)]">
            @lang("Don't have an account?")
            <a href="{{ route('register') }}" class="font-medium text-[var(--color-foreground)] hover:underline no-underline">
                @lang('Register')
            </a>
        </p>
    </div>
</div>
