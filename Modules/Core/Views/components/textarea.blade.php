@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'help' => null,
    'rows' => 4,
    'model' => null,
    'debounce' => null,
])

@php
    $inputId = $id ?? $name;
    $wireModel = $model ? "wire:model" . ($debounce ? ".live.debounce.{$debounce}ms" : ".live") . "=\"{$model}\"" : '';
    $hasError = $error ?? (isset($name) && $errors->has($name));
    $errorText = $error ?? (isset($name) ? $errors->first($name) : null);

    $baseClasses = 'block w-full rounded-lg border px-3 py-2 text-sm transition-colors duration-150 placeholder:text-[var(--color-foreground-muted)] focus:outline-none focus:ring-2 focus:ring-offset-0';
    $normalClasses = 'border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-foreground)] focus:border-[var(--color-primary-500)] focus:ring-[var(--color-primary-500)]';
    $errorClasses = 'border-[var(--color-danger-500)] bg-[var(--color-surface)] text-[var(--color-foreground)] focus:border-[var(--color-danger-500)] focus:ring-[var(--color-danger-500)]';
    $disabledClasses = 'opacity-50 cursor-not-allowed bg-[var(--color-surface-secondary)]';
    $classes = trim("{$baseClasses} " . ($hasError ? $errorClasses : $normalClasses) . ($disabled ? " {$disabledClasses}" : ''));
@endphp

<div>
    @if ($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-[var(--color-foreground)] mb-1">
            {{ $label }}
            @if ($required)
                <span class="text-[var(--color-danger-500)]">*</span>
            @endif
        </label>
    @endif

    <textarea
        id="{{ $inputId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        {{ $wireModel ? $wireModel : '' }}
        class="{{ $classes }}"
        {{ $attributes }}
>{{ old($name) }}</textarea>

    @if ($help && !$hasError)
        <p class="mt-1 text-xs text-[var(--color-foreground-muted)]">{{ $help }}</p>
    @endif

    @if ($hasError && $errorText)
        <p class="mt-1 text-xs text-[var(--color-danger-600)]">{{ $errorText }}</p>
    @endif
</div>
