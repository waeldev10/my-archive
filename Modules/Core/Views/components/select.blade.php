@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'help' => null,
    'placeholder' => null,
    'options' => [],
    'selected' => null,
    'model' => null,
])

@php
    $inputId = $id ?? $name;
    $wireModel = $model ? "wire:model.live=\"{$model}\"" : '';
    $hasError = $error ?? (isset($name) && $errors->has($name));
    $errorText = $error ?? (isset($name) ? $errors->first($name) : null);

    $baseClasses = 'block w-full rounded-lg border px-3 py-2 text-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-0';
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

    <select
        id="{{ $inputId }}"
        name="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $wireModel ? $wireModel : '' }}
        class="{{ $classes }}"
        {{ $attributes }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $value => $label)
            <option value="{{ $value }}" {{ (string) $selected === (string) $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>

    @if ($help && !$hasError)
        <p class="mt-1 text-xs text-[var(--color-foreground-muted)]">{{ $help }}</p>
    @endif

    @if ($hasError && $errorText)
        <p class="mt-1 text-xs text-[var(--color-danger-600)]">{{ $errorText }}</p>
    @endif
</div>
