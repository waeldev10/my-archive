@props([
    'padding' => true,
    'border' => true,
    'shadow' => 'sm', // sm, md, lg, none
    'class' => '',
])

@php
    $shadowClasses = match ($shadow) {
        'sm' => 'shadow-sm',
        'md' => 'shadow-md',
        'lg' => 'shadow-lg',
        'none' => '',
        default => 'shadow-sm',
    };

    $classes = trim(
        'bg-[var(--color-surface)] rounded-lg ' .
        ($border ? 'border border-[var(--color-border)] ' : '') .
        $shadowClasses . ' ' .
        ($padding ? 'p-5 ' : '') .
        $class
    );
@endphp

<div class="{{ $classes }}" {{ $attributes }}>
    {{ $slot }}
</div>
