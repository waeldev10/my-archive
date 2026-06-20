@props([
    'variant' => 'primary', // primary, secondary, danger, ghost
    'size' => 'md',        // sm, md, lg
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'loading' => false,
    'wire' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-primary-500)] no-underline';

    $variantClasses = match ($variant) {
        'primary' => 'bg-[var(--color-primary-600)] text-white hover:bg-[var(--color-primary-700)] active:bg-[var(--color-primary-800)] disabled:opacity-50 disabled:cursor-not-allowed',
        'secondary' => 'bg-[var(--color-surface)] text-[var(--color-foreground)] border border-[var(--color-border)] hover:bg-[var(--color-surface-secondary)] active:bg-[var(--color-surface-tertiary)] disabled:opacity-50 disabled:cursor-not-allowed',
        'danger' => 'bg-[var(--color-danger-600)] text-white hover:bg-[var(--color-danger-700)] active:bg-[var(--color-danger-800)] disabled:opacity-50 disabled:cursor-not-allowed',
        'ghost' => 'text-[var(--color-foreground-secondary)] hover:text-[var(--color-foreground)] hover:bg-[var(--color-surface-secondary)] active:bg-[var(--color-surface-tertiary)] disabled:opacity-50 disabled:cursor-not-allowed',
        default => 'bg-[var(--color-primary-600)] text-white hover:bg-[var(--color-primary-700)]',
    };

    $sizeClasses = match ($size) {
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
        default => 'px-4 py-2 text-sm',
    };

    $classes = trim("{$baseClasses} {$variantClasses} {$sizeClasses}");

    $attrs = $attributes->merge([
        'class' => $classes,
        'disabled' => $disabled || $loading ? true : null,
    ]);

    // Wire:loading support
    $loadingAttr = $wire ? "wire:target=\"{$wire}\"" : '';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attrs }}>
        @if ($loading)
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attrs }}>
        @if ($loading)
            <svg wire:loading {{ $loadingAttr ? "wire:target=\"{$wire}\"" : '' }} class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
        @endif
        <span @if($loading && $wire) wire:loading.remove wire:target="{{ $wire }}" @endif>{{ $slot }}</span>
        @if($loading && $wire)
            <span wire:loading wire:target="{{ $wire }}" class="sr-only">@lang('Loading...')</span>
        @endif
    </button>
@endif
