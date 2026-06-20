@props([
    'icon' => null,
    'title' => null,
    'description' => null,
    'action' => null,
    'actionLabel' => null,
])

<div class="text-center py-12" {{ $attributes }}>
    @if ($icon)
        <div class="mx-auto mb-4 text-[var(--color-foreground-muted)]">
            {!! $icon !!}
        </div>
    @else
        <div class="mx-auto mb-4">
            <svg class="h-12 w-12 mx-auto text-[var(--color-foreground-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
        </div>
    @endif

    @if ($title)
        <h3 class="text-lg font-semibold text-[var(--color-foreground)] mb-1">{{ $title }}</h3>
    @endif

    @if ($description)
        <p class="text-sm text-[var(--color-foreground-secondary)] max-w-md mx-auto">{{ $description }}</p>
    @endif

    @if ($action && $actionLabel)
        <div class="mt-6">
            <a href="{{ $action }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-[var(--color-primary-600)] rounded-lg hover:bg-[var(--color-primary-700)] transition-colors no-underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ $actionLabel }}
            </a>
        </div>
    @endif

    {{ $slot }}
</div>
