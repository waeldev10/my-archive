<div>
    <div class="mx-auto max-w-md">
        <h1 class="text-2xl font-bold text-[var(--color-foreground)]">@lang('Reset Password')</h1>
        <p class="mt-2 text-sm text-[var(--color-foreground-secondary)]">
            @lang('Enter your email address and we\'ll send you a reset link.')
        </p>

        <form wire:submit="sendResetLink" class="mt-6 space-y-4">
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

            {{-- Success --}}
            @if ($successMessage)
                <x-core::alert type="success" :message="$successMessage" />
            @endif

            {{-- Error --}}
            @if ($errorMessage)
                <x-core::alert type="danger" :message="$errorMessage" />
            @endif

            {{-- Submit --}}
            <button type="submit" wire:loading.attr="disabled"
                    class="w-full rounded-lg bg-[var(--color-foreground)] px-4 py-2 text-sm font-medium text-[var(--color-foreground-inverse)] hover:opacity-90 disabled:opacity-50 transition-opacity cursor-pointer">
                <span wire:loading.remove wire:target="sendResetLink">@lang('Send Reset Link')</span>
                <span wire:loading wire:target="sendResetLink">@lang('Sending...')</span>
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-[var(--color-foreground-secondary)]">
            @lang('Remember your password?')
            <a href="{{ route('login') }}" class="font-medium text-[var(--color-foreground)] hover:underline no-underline">
                @lang('Log In')
            </a>
        </p>
    </div>
</div>
