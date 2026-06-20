@extends('core::layouts.app', ['title' => $typeLabel . 's — ' . config('app.name')])

@section('content')
@php
    use Modules\Archives\Services\ArchiveFactory;

    $factory = app(ArchiveFactory::class);
    $allTypes = $factory->allTypes();
@endphp

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <x-core::page-header
        :title="$typeLabel . 's'"
        :description="__('Manage your archived :type', ['type' => strtolower($typeLabel)])"
    >
        <x-slot:actions>
            {{-- Archive Type Switcher --}}
            <div class="relative" x-data="{ open: false }">
                <x-core::button variant="secondary" x-on:click="open = !open">
                    <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    {{ $typeLabel }}
                </x-core::button>
                <div x-show="open"
                     x-on:click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute {{ app()->getLocale() === 'ar' ? 'left-0' : 'right-0' }} mt-2 w-48 bg-[var(--color-surface)] rounded-lg shadow-lg border border-[var(--color-border)] z-50 max-h-72 overflow-y-auto">
                    @foreach ($allTypes as $t)
                        <a href="{{ route('archives.list', ['type' => $t]) }}"
                           wire:navigate
                           class="block px-4 py-2 text-sm text-[var(--color-foreground)] hover:bg-[var(--color-surface-secondary)] no-underline {{ $t === $type ? 'font-semibold bg-[var(--color-surface-secondary)]' : '' }}">
                            {{ ucfirst($t) }}
                        </a>
                    @endforeach
                </div>
            </div>

            <x-core::button variant="primary" :href="route('archives.create', ['type' => $type])">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                @lang('New :type', ['type' => $typeLabel])
            </x-core::button>
        </x-slot:actions>
    </x-core::page-header>

    {{-- Livewire Component --}}
    @livewire('archives.list', ['type' => $type], key($type))
</div>
@endsection
