<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-[var(--color-foreground)]">@lang('Dashboard')</h1>
        <p class="mt-1 text-sm text-[var(--color-foreground-secondary)]">
            @lang('Welcome back! Here\'s an overview of your archives.')
        </p>
    </div>

    {{-- Empty State --}}
    <x-core::empty-state
        title="No archives yet"
        description="Create your first archive to start building your personal knowledge base."
    >
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('archives.create', ['type' => 'note']) }}"
               wire:navigate
               class="rounded-lg bg-[var(--color-primary-600)] px-4 py-2 text-sm font-medium text-white hover:bg-[var(--color-primary-700)] transition-colors no-underline">
                @lang('New Note')
            </a>
            <a href="{{ route('archives.create', ['type' => 'link']) }}"
               wire:navigate
               class="rounded-lg border border-[var(--color-border)] px-4 py-2 text-sm font-medium text-[var(--color-foreground)] hover:bg-[var(--color-surface-secondary)] transition-colors no-underline">
                @lang('New Link')
            </a>
            <a href="{{ route('archives.create', ['type' => 'todo']) }}"
               wire:navigate
               class="rounded-lg border border-[var(--color-border)] px-4 py-2 text-sm font-medium text-[var(--color-foreground)] hover:bg-[var(--color-surface-secondary)] transition-colors no-underline">
                @lang('New Todo')
            </a>
        </div>
    </x-core::empty-state>
</div>
