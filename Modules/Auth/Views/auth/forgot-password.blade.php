<div>
    <div class="mx-auto max-w-md">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reset Password</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Enter your email address and we'll send you a reset link.
        </p>

        <form wire:submit="sendResetLink" class="mt-6 space-y-4">
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input
                    wire:model.blur="email"
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-gray-400"
                    required
                    autofocus
                />
                @error('email') <span class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
            </div>

            @if ($successMessage)
                <div class="rounded-lg bg-green-50 p-3 text-sm text-green-800 dark:bg-green-900/50 dark:text-green-200">
                    {{ $successMessage }}
                </div>
            @endif

            @if ($errorMessage)
                <div class="rounded-lg bg-red-50 p-3 text-sm text-red-800 dark:bg-red-900/50 dark:text-red-200">
                    {{ $errorMessage }}
                </div>
            @endif

            <button type="submit" class="w-full rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200">
                {{ $isLoading ? 'Sending...' : 'Send Reset Link' }}
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
            Remember your password?
            <a href="{{ route('login') }}" class="font-medium text-gray-900 hover:underline dark:text-white">
                Log In
            </a>
        </p>
    </div>
</div>
