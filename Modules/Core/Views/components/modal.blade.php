@props([
    'name' => 'modal',
    'title' => null,
    'maxWidth' => 'lg', // sm, md, lg, xl, 2xl
    'show' => false,
])

@php
    $maxWidthClasses = match ($maxWidth) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        default => 'max-w-lg',
    };
@endphp

<div x-data="{ open: {{ $show ? 'true' : 'false' }} }"
     x-init="$watch('open', value => { if (value) document.body.style.overflow = 'hidden'; else document.body.style.overflow = ''; })"
     x-show="open"
     x-cloak
     {{ $attributes->merge(['class' => 'fixed inset-0 z-50 overflow-y-auto']) }}
     role="dialog"
     aria-modal="true"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    {{-- Backdrop --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm"
         @click="open = false"
         aria-hidden="true">
    </div>

    {{-- Panel --}}
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="open = false"
             class="relative w-full {{ $maxWidthClasses }} bg-[var(--color-surface)] rounded-xl shadow-xl border border-[var(--color-border)]">

            {{-- Header --}}
            @if ($title)
                <div class="flex items-center justify-between px-6 py-4 border-b border-[var(--color-border)]">
                    <h2 class="text-lg font-semibold text-[var(--color-foreground)]">
                        {{ $title }}
                    </h2>
                    <button type="button" @click="open = false"
                            class="text-[var(--color-foreground-muted)] hover:text-[var(--color-foreground)] cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Body --}}
            <div class="px-6 py-4">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            @isset($footer)
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-[var(--color-border)] bg-[var(--color-surface-secondary)] rounded-b-xl">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
