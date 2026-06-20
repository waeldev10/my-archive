@php
    use Modules\Archives\Services\ArchiveFactory;

    $factory = app(ArchiveFactory::class);
    $typeLabel = ucfirst($this->type);
    $allTypes = $factory->allTypes();
@endphp

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ $typeLabel }}s
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Manage your archived {{ $typeLabel }}s
            </p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Archive Type Switcher --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    {{ $typeLabel }}
                </button>
                <div x-show="open"
                     @click.away="open = false"
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 max-h-72 overflow-y-auto">
                    @foreach ($allTypes as $t)
                        <a href="{{ route('archives.list', ['type' => $t]) }}"
                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ $t === $this->type ? 'font-semibold bg-gray-100 dark:bg-gray-700' : '' }}">
                            {{ ucfirst($t) }}
                        </a>
                    @endforeach
                </div>
            </div>
            <a href="{{ route('archives.create', ['type' => $this->type]) }}"
               wire:navigate
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New {{ $typeLabel }}
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            {{-- Search --}}
            <div class="flex-1">
                <label for="search" class="sr-only">Search</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="search"
                           type="text"
                           id="search"
                           placeholder="Search {{ $typeLabel }}s..."
                           class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            {{-- Sort --}}
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600 dark:text-gray-400">Sort:</label>
                <select wire:model.live="sort"
                        class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 py-2 px-3 focus:ring-2 focus:ring-indigo-500">
                    <option value="created_at">Date Created</option>
                    <option value="title">Title</option>
                    <option value="updated_at">Last Updated</option>
                </select>
                <button wire:click="$set('order', '{{ $order === 'desc' ? 'asc' : 'desc' }}')"
                        class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg">
                    @if ($order === 'desc')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                        </svg>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/>
                        </svg>
                    @endif
                </button>
            </div>

            {{-- Favorites Filter --}}
            <div class="flex items-center gap-2">
                <button wire:click="$set('favorite', {{ $favorite === true ? 'null' : 'true' }})"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm rounded-lg border transition-colors
                            {{ $favorite === true
                                ? 'bg-yellow-50 border-yellow-300 text-yellow-700 dark:bg-yellow-900/20 dark:border-yellow-600 dark:text-yellow-400'
                                : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <svg class="w-4 h-4" fill="{{ $favorite === true ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    Favorites
                </button>
                @if ($favorite !== null)
                    <button wire:click="$set('favorite', null)"
                            class="text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        Clear
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Archives List --}}
    <div class="space-y-4">
        @forelse ($this->archives as $archive)
            <div wire:key="{{ $archive->id }}"
                 class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            {{-- Favorite & Title --}}
                            <div class="flex items-center gap-2 mb-1">
                                <button wire:click="toggleFavorite('{{ $archive->id }}')"
                                        class="shrink-0 p-0.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-5 h-5 {{ $archive->is_favorite ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                                         fill="{{ $archive->is_favorite ? 'currentColor' : 'none' }}"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                </button>
                                <a href="{{ route('archives.show', ['type' => $this->type, 'archive' => $archive->id]) }}"
                                   wire:navigate
                                   class="text-lg font-semibold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 truncate">
                                    {{ $archive->title }}
                                </a>
                            </div>

                            {{-- Description snippet --}}
                            @if ($archive->description)
                                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mt-1">
                                    {{ Str::limit(strip_tags($archive->description), 200) }}
                                </p>
                            @endif

                            {{-- Tags --}}
                            @if ($archive->tags->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    @foreach ($archive->tags as $tag)
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('archives.edit', ['type' => $this->type, 'archive' => $archive->id]) }}"
                               wire:navigate
                               class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                               title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <button wire:click="delete('{{ $archive->id }}')"
                                    wire:confirm="Are you sure you want to move this archive to trash?"
                                    class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                    title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Meta --}}
                    <div class="flex items-center gap-4 mt-3 text-xs text-gray-400 dark:text-gray-500">
                        <span>Created {{ $archive->created_at->diffForHumans() }}</span>
                        @if ($archive->updated_at !== $archive->created_at)
                            <span>Updated {{ $archive->updated_at->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No {{ $typeLabel }}s found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if ($search)
                        No results matching "{{ $search }}". Try a different search term.
                    @else
                        Get started by creating your first {{ strtolower($typeLabel) }}.
                    @endif
                </p>
                @unless ($search)
                    <a href="{{ route('archives.create', ['type' => $this->type]) }}"
                       wire:navigate
                       class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create {{ $typeLabel }}
                    </a>
                @endunless
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($this->archives->hasPages())
        <div class="mt-6">
            {{ $this->archives->links(data: ['scrollTo' => false]) }}
        </div>
    @endif
</div>
