@props([
    'type' => 'info', // info, success, warning, danger
    'message' => null,
    'dismissible' => false,
    'title' => null,
    'icon' => null,
])

@php
    $variantClasses = match ($type) {
        'info' => 'bg-[var(--color-info-50)] border-[var(--color-info-200)] text-[var(--color-info-800)] dark:bg-[var(--color-info-950)] dark:border-[var(--color-info-800)] dark:text-[var(--color-info-200)]',
        'success' => 'bg-[var(--color-success-50)] border-[var(--color-success-200)] text-[var(--color-success-800)] dark:bg-[var(--color-success-950)] dark:border-[var(--color-success-800)] dark:text-[var(--color-success-200)]',
        'warning' => 'bg-[var(--color-warning-50)] border-[var(--color-warning-200)] text-[var(--color-warning-800)] dark:bg-[var(--color-warning-950)] dark:border-[var(--color-warning-800)] dark:text-[var(--color-warning-200)]',
        'danger' => 'bg-[var(--color-danger-50)] border-[var(--color-danger-200)] text-[var(--color-danger-800)] dark:bg-[var(--color-danger-950)] dark:border-[var(--color-danger-800)] dark:text-[var(--color-danger-200)]',
        default => 'bg-[var(--color-info-50)] border-[var(--color-info-200)] text-[var(--color-info-800)] dark:bg-[var(--color-info-950)] dark:border-[var(--color-info-800)] dark:text-[var(--color-info-200)]',
    };

    $defaultIcons = [
        'info' => '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'success' => '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        'warning' => '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>',
        'danger' => '<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    ];

    $iconSvg = $icon ?? $defaultIcons[$type] ?? $defaultIcons['info'];
@endphp

<div x-data="{ visible: true }" x-show="visible" x-transition:leave.duration.300ms
     class="flex items-start gap-3 rounded-lg border p-4 text-sm {{ $variantClasses }}"
     role="alert"
     {{ $attributes }}>
    @if ($icon !== false)
        {!! $iconSvg !!}
    @endif

    <div class="flex-1 min-w-0">
        @if ($title)
            <p class="font-medium mb-1">{{ $title }}</p>
        @endif
        <p>{{ $message ?? $slot }}</p>
    </div>

    @if ($dismissible)
        <button type="button" @click="visible = false" class="shrink-0 hover:opacity-70 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    @endif
</div>
