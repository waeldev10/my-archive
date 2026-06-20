@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'id' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'help' => null,
    'leadingIcon' => null,
    'trailingIcon' => null,
    'model' => null,
    'debounce' => null,
    'blur' => false,
])

@php
    $inputId = $id ?? $name;
    $wireModel = $model ? "wire:model" . ($blur ? ".blur" : ($debounce ? ".live.debounce.{$debounce}ms" : ".blur")) . "=\"{$model}\"" : '';
    $hasError = $error ?? (isset($name) && $errors->has($name));
    $errorText = $error ?? (isset($name) ? $errors->first($name) : null);

    $baseClasses = 'block w-full rounded-lg border px-3 py-2 text-sm transition-colors duration-150 placeholder:text-[var(--color-foreground-muted)] focus:outline-none focus:ring-2 focus:ring-offset-0';
    $normalClasses = 'border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-foreground)] focus:border-[var(--color-primary-500)] focus:ring-[var(--color-primary-500)]';
    $errorClasses = 'border-[var(--color-danger-500)] bg-[var(--color-surface)] text-[var(--color-foreground)] focus:border-[var(--color-danger-500)] focus:ring-[var(--color-danger-500)]';
    $disabledClasses = 'opacity-50 cursor-not-allowed bg-[var(--color-surface-secondary)]';
    $leadingPadding = $leadingIcon ? 'ps-10' : '';
    $trailingPadding = $trailingIcon ? 'pe-10' : '';

    $classes = trim("{$baseClasses} " . ($hasError ? $errorClasses : $normalClasses) . ($disabled ? " {$disabledClasses}" : '') . " {$leadingPadding} {$trailingPadding}");
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

    <div class="relative">
        @if ($leadingIcon)
            <div class="pointer-events-none absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'right-0 pe-3' : 'left-0 pl-3' }} flex items-center">
                {!! $leadingIcon !!}
            </div>
        @endif

        <input
            type="{{ $type }}"
            id="{{ $inputId }}"
            name="{{ $name }}"
            value="{{ old($name) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $readonly ? 'readonly' : '' }}
            {{ $wireModel ? $wireModel : '' }}
            class="{{ $classes }}"
            @if ($type === 'number') inputmode="decimal" @endif
            @if ($type === 'tel') inputmode="tel" @endif
            {{ $attributes }}
        >

        @if ($trailingIcon)
            <div class="pointer-events-none absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'left-0 ps-3' : 'right-0 pr-3' }} flex items-center">
                {!! $trailingIcon !!}
            </div>
        @endif
    </div>

    @if ($help && !$hasError)
        <p class="mt-1 text-xs text-[var(--color-foreground-muted)]">{{ $help }}</p>
    @endif

    @if ($hasError && $errorText)
        <p class="mt-1 text-xs text-[var(--color-danger-600)]">{{ $errorText }}</p>
    @endif
</div>
