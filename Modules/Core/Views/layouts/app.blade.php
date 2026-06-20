<!DOCTYPE html>
<html lang="ar" dir="rtl"
      class="{{ session('theme', 'system') }}"
      x-data
      :class="$store.theme.isDark ? 'dark' : ''">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0a0a0a" media="(prefers-color-scheme: dark)">

    <title>{{ $title ?? config('app.name', 'My Archive') }}</title>

    {{-- Fonts: Tajawal via Vite Bunny CDN — see vite.config.js --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="min-h-screen bg-[var(--color-background)] text-[var(--color-foreground)] antialiased">

    {{-- Theme pre-boot: apply stored preference before Alpine hydrates — prevents flash --}}
    <script>
        (function () {
            var theme = localStorage.getItem('theme') || 'system';
            var isDark = theme === 'dark' ||
                (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    {{-- Navigation --}}
    <header class="border-b border-[var(--color-border)] bg-[var(--color-surface)]">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
            {{-- Logo / Brand --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}"
                   class="text-xl font-semibold text-[var(--color-foreground)] no-underline hover:opacity-80">
                    {{ config('app.name', 'My Archive') }}
                </a>

                @auth
                    <nav class="hidden items-center gap-6 md:flex" aria-label="Main navigation">
                        <a href="{{ route('dashboard') }}"
                           class="text-sm font-medium text-[var(--color-foreground-secondary)] hover:text-[var(--color-foreground)] transition-colors no-underline">
                            @lang('Dashboard')
                        </a>
                        <a href="{{ route('archives.list', ['type' => 'note']) }}"
                           class="text-sm font-medium text-[var(--color-foreground-secondary)] hover:text-[var(--color-foreground)] transition-colors no-underline">
                            @lang('Archives')
                        </a>
                    </nav>
                @endauth
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-4">
                @auth
                    @if (Route::has('settings'))
                        <a href="{{ route('settings') }}"
                           class="text-sm font-medium text-[var(--color-foreground-secondary)] hover:text-[var(--color-foreground)] transition-colors no-underline">
                            @lang('Settings')
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="text-sm font-medium text-[var(--color-foreground-secondary)] hover:text-[var(--color-foreground)] transition-colors cursor-pointer">
                            @lang('Log Out')
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="text-sm font-medium text-[var(--color-foreground-secondary)] hover:text-[var(--color-foreground)] transition-colors no-underline">
                        @lang('Log In')
                    </a>
                    <a href="{{ route('register') }}"
                       class="rounded-lg bg-[var(--color-foreground)] px-4 py-2 text-sm font-medium text-[var(--color-foreground-inverse)] hover:opacity-90 transition-opacity no-underline">
                        @lang('Register')
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-core::alert type="success" :message="session('success')" dismissible />
        </div>
    @endif

    @if (session('error'))
        <div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-core::alert type="danger" :message="session('error')" dismissible />
        </div>
    @endif

    {{-- Main Content --}}
    {{-- Supports both @extends (via @yield) and Livewire component layout (via $slot) --}}
    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        @hasSection('content')
            @yield('content')
        @else
            {{ $slot }}
        @endif
    </main>

    @stack('scripts')
</body>
</html>
