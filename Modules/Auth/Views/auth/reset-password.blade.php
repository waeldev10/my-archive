<div>
    <div class="mx-auto max-w-md">
        <h1 class="text-2xl font-bold text-[var(--color-foreground)]">@lang('Set New Password')</h1>
        <p class="mt-2 text-sm text-[var(--color-foreground-secondary)]">
            @lang('Enter your new password below.')
        </p>

        <form wire:submit="resetPassword" class="mt-6 space-y-4">
            {{-- Email --}}
            <x-core::input
                label="Email"
                name="email"
                type="email"
                model="email"
                blur
                required
            />

            {{-- New Password --}}
            <x-core::input
                label="New Password"
                name="password"
                type="password"
                model="password"
                blur
                required
            />

            {{-- Confirm Password --}}
            <x-core::input
                label="Confirm New Password"
                name="password_confirmation"
                type="password"
                model="password_confirmation"
                blur
                required
            />

            {{-- Error --}}
            @if ($errorMessage)
                <x-core::alert type="danger" :message="$errorMessage" />
            @endif

            {{-- Submit --}}
            <button type="submit" wire:loading.attr="disabled"
                    class="w-full rounded-lg bg-[var(--color-foreground)] px-4 py-2 text-sm font-medium text-[var(--color-foreground-inverse)] hover:opacity-90 disabled:opacity-50 transition-opacity cursor-pointer">
                <span wire:loading.remove wire:target="resetPassword">@lang('Reset Password')</span>
                <span wire:loading wire:target="resetPassword">@lang('Resetting...')</span>
            </button>
        </form>
    </div>
</div>
