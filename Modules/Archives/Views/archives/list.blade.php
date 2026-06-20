@php
    use Modules\Archives\Services\ArchiveFactory;

    $factory = app(ArchiveFactory::class);
    $typeLabel = ucfirst($this->type);
    $allTypes = $factory->allTypes();
@endphp

<div>
    {{-- Filters --}}
    <x-core::card class="mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            {{-- Search --}}
            <div class="flex-1">
                <label for="search" class="sr-only">@lang('Search')</label>
                <div class="relative">
                    <svg class="absolute {{ app()->getLocale() === 'ar' ? 'right-3' : 'left-3' }} top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-foreground-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.throttle.300ms="search"
                           type="search"
                           id="search"
                           placeholder="{{ __('Search :type...', ['type' => $typeLabel . 's']) }}"
                           class="w-full {{ app()->getLocale() === 'ar' ? 'pr-10' : 'pl-10' }} px-4 py-2 text-sm border border-[var(--color-border)] rounded-lg bg-[var(--color-surface)] text-[var(--color-foreground)] placeholder:text-[var(--color-foreground-muted)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary-500)] focus:border-transparent">
                </div>
            </div>

            {{-- Sort --}}
            <div class="flex items-center gap-2">
                <label class="text-sm text-[var(--color-foreground-secondary)] whitespace-nowrap">@lang('Sort:'):</label>
                <select wire:model.live="sort"
                        class="text-sm border border-[var(--color-border)] rounded-lg bg-[var(--color-surface)] text-[var(--color-foreground)] py-2 px-3 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary-500)]">
                    <option value="created_at">@lang('Date Created')</option>
                    <option value="title">@lang('Title')</option>
                    <option value="updated_at">@lang('Last Updated')</option>
                </select>
                <button wire:click="$set('order', '{{ $order === 'desc' ? 'asc' : 'desc' }}')"
                        class="p-2 text-[var(--color-foreground-muted)] hover:text-[var(--color-foreground)] border border-[var(--color-border)] rounded-lg hover:bg-[var(--color-surface-secondary)] transition-colors cursor-pointer">
                    @if ($order === 'desc')
                        <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                        </svg>
                    @else
                        <svg class="w-4 h-4 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/>
                        </svg>
                    @endif
                </button>
            </div>

            {{-- Favorites Filter --}}
            <div class="flex items-center gap-2">
                <button wire:click="$set('favorite', {{ $favorite === true ? 'null' : 'true' }})"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg border transition-colors cursor-pointer
                            {{ $favorite === true
                                ? 'bg-[var(--color-warning-50)] border-[var(--color-warning-300)] text-[var(--color-warning-700)] dark:bg-[var(--color-warning-950)] dark:border-[var(--color-warning-700)] dark:text-[var(--color-warning-300)]'
                                : 'border-[var(--color-border)] text-[var(--color-foreground-secondary)] hover:bg-[var(--color-surface-secondary)]' }}">
                    <svg class="w-4 h-4" fill="{{ $favorite === true ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    @lang('Favorites')
                </button>
                @if ($favorite !== null)
                    <button wire:click="$set('favorite', null)"
                            class="text-xs text-[var(--color-foreground-muted)] hover:text-[var(--color-foreground)] cursor-pointer">
                        @lang('Clear')
                    </button>
                @endif
            </div>
        </div>
    </x-core::card>

    {{-- Archives List --}}
    <div class="space-y-4">
        @forelse ($this->archives as $archive)
            <div wire:key="{{ $archive->id }}"
                 class="bg-[var(--color-surface)] rounded-lg shadow-sm border border-[var(--color-border)] hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            {{-- Favorite & Title --}}
                            <div class="flex items-center gap-2 mb-1">
                                <button wire:click="toggleFavorite('{{ $archive->id }}')"
                                        class="shrink-0 p-0.5 rounded hover:bg-[var(--color-surface-secondary)] transition-colors cursor-pointer">
                                    <svg class="w-5 h-5 {{ $archive->is_favorite ? 'text-[var(--color-warning-500)] fill-[var(--color-warning-500)]' : 'text-[var(--color-foreground-muted)]' }}"
                                         fill="{{ $archive->is_favorite ? 'currentColor' : 'none' }}"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                </button>
                                <a href="{{ route('archives.show', ['type' => $this->type, 'archive' => $archive->id]) }}"
                                   wire:navigate
                                   class="text-lg font-semibold text-[var(--color-foreground)] hover:text-[var(--color-primary-600)] no-underline truncate">
                                    {{ $archive->title }}
                                </a>
                            </div>

                            {{-- Description snippet --}}
                            @if ($archive->description)
                                <p class="text-sm text-[var(--color-foreground-secondary)] line-clamp-2 mt-1">
                                    {{ Str::limit(strip_tags($archive->description), 200) }}
                                </p>
                            @endif

                            {{-- Tags --}}
                            @if ($archive->tags->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    @foreach ($archive->tags as $tag)
                                        <x-core::badge variant="primary">{{ $tag->name }}</x-core::badge>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('archives.edit', ['type' => $this->type, 'archive' => $archive->id]) }}"
                               wire:navigate
                               class="p-2 text-[var(--color-foreground-muted)] hover:text-[var(--color-foreground)] rounded-lg hover:bg-[var(--color-surface-secondary)] transition-colors"
                               title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <button wire:click="delete('{{ $archive->id }}')"
                                    wire:confirm="Are you sure you want to move this archive to trash?"
                                    class="p-2 text-[var(--color-foreground-muted)] hover:text-[var(--color-danger-600)] rounded-lg hover:bg-[var(--color-surface-secondary)] transition-colors cursor-pointer"
                                    title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Meta --}}
                    <div class="flex items-center gap-4 mt-3 text-xs text-[var(--color-foreground-muted)]">
                        <span>@lang('Created') {{ $archive->created_at->diffForHumans() }}</span>
                        @if ($archive->updated_at !== $archive->created_at)
                            <span>@lang('Updated') {{ $archive->updated_at->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <x-core::empty-state
                :title="__('No :type found', ['type' => $typeLabel . 's'])"
            >
                @if ($search)
                    <p class="text-sm text-[var(--color-foreground-secondary)]">
                        @lang('No results matching ":search". Try a different search term.', ['search' => $search])
                    </p>
                @else
                    <p class="text-sm text-[var(--color-foreground-secondary)]">
                        @lang('Get started by creating your first :type.', ['type' => strtolower($typeLabel)])
                    </p>
                    <x-core::button variant="primary" :href="route('archives.create', ['type' => $this->type])" class="mt-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        @lang('Create :type', ['type' => $typeLabel])
                    </x-core::button>
                @endif
            </x-core::empty-state>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($this->archives->hasPages())
        <div class="mt-6">
            {{ $this->archives->links(data: ['scrollTo' => false]) }}
        </div>
    @endif
</div>
