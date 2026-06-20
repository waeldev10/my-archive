@props([
    'title' => null,
    'description' => null,
    'backRoute' => null,
    'backLabel' => null,
    'actions' => null,
])

<div class="mb-8" {{ $attributes }}>
    @if ($backRoute)
        <a href="{{ $backRoute }}"
           wire:navigate
           class="inline-flex items-center gap-1 text-sm text-[var(--color-foreground-secondary)] hover:text-[var(--color-foreground)] transition-colors mb-4 no-underline">
            {{-- RTL-aware back arrow --}}
            <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ $backLabel ?? __('Back') }}
        </a>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            @if ($title)
                <h1 class="text-2xl sm:text-3xl font-bold text-[var(--color-foreground)]">
                    {{ $title }}
                </h1>
            @endif
            @if ($description)
                <p class="mt-1 text-sm text-[var(--color-foreground-secondary)]">
                    {{ $description }}
                </p>
            @endif
        </div>

        @if ($actions)
            <div class="flex items-center gap-3 shrink-0">
                {{ $actions }}
            </div>
        @endif
    </div>

    {{ $slot }}
</div>
