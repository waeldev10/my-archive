@props([
    'variant' => 'primary', // primary, secondary, success, warning, danger, accent
    'size' => 'sm',         // sm, md
    'removable' => false,
    'href' => null,
])

@php
    $variantClasses = match ($variant) {
        'primary' => 'bg-[var(--color-primary-50)] text-[var(--color-primary-700)] dark:bg-[var(--color-primary-950)] dark:text-[var(--color-primary-300)]',
        'secondary' => 'bg-[var(--color-surface-secondary)] text-[var(--color-foreground-secondary)] dark:bg-[var(--color-surface-dark-secondary)] dark:text-[var(--color-foreground-secondary)] border border-[var(--color-border)]',
        'success' => 'bg-[var(--color-success-50)] text-[var(--color-success-700)] dark:bg-[var(--color-success-950)] dark:text-[var(--color-success-300)]',
        'warning' => 'bg-[var(--color-warning-50)] text-[var(--color-warning-700)] dark:bg-[var(--color-warning-950)] dark:text-[var(--color-warning-300)]',
        'danger' => 'bg-[var(--color-danger-50)] text-[var(--color-danger-700)] dark:bg-[var(--color-danger-950)] dark:text-[var(--color-danger-300)]',
        'accent' => 'bg-[var(--color-accent-50)] text-[var(--color-accent-700)] dark:bg-[var(--color-accent-950)] dark:text-[var(--color-accent-300)]',
        default => 'bg-[var(--color-primary-50)] text-[var(--color-primary-700)] dark:bg-[var(--color-primary-950)] dark:text-[var(--color-primary-300)]',
    };

    $sizeClasses = $size === 'sm' ? 'px-2 py-0.5 text-xs' : 'px-3 py-1 text-sm';

    $classes = trim("inline-flex items-center gap-1 font-medium rounded-full {$variantClasses} {$sizeClasses}");
@endphp

@if ($href)
    <a href="{{ $href }}" class="{{ $classes }} no-underline" {{ $attributes }}>
        {{ $slot }}
    </a>
@else
    <span class="{{ $classes }}" {{ $attributes }}>
        {{ $slot }}
        @if ($removable)
            <button type="button" class="shrink-0 hover:opacity-70 cursor-pointer" {{ $attributes->get('x-on:remove') ? '' : '' }}>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        @endif
    </span>
@endif
