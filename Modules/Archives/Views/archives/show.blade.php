<div>
    @php $archive = $this->archive; @endphp

    @if ($archive === null)
        <x-core::empty-state
            title="Archive not found"
            description="The archive you're looking for doesn't exist or has been deleted."
        >
            <x-core::button variant="primary" :href="route('archives.list', ['type' => $this->type])">
                @lang('Back to list')
            </x-core::button>
        </x-core::empty-state>
    @else
        {{-- Header --}}
        <div class="mb-8">
            <x-core::page-header
                :title="$archive->title"
                :back-route="route('archives.list', ['type' => $this->type])"
                :back-label="__('Back to :type', ['type' => ucfirst($this->type) . 's'])"
            >
                <x-slot:actions>
                    <x-core::button variant="secondary" :href="route('archives.edit', ['type' => $this->type, 'archive' => $archive->id])">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        @lang('Edit')
                    </x-core::button>
                    <x-core::button variant="danger" wire:click="delete" wire:confirm="Are you sure you want to move this archive to trash?">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        @lang('Delete')
                    </x-core::button>
                </x-slot:actions>
            </x-core::page-header>

            {{-- Favorite & Meta --}}
            <div class="flex flex-wrap items-center gap-4 mt-3 text-sm text-[var(--color-foreground-secondary)]">
                <button wire:click="toggleFavorite"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-[var(--color-surface-secondary)] transition-colors cursor-pointer">
                    <svg class="w-5 h-5 {{ $archive->is_favorite ? 'text-[var(--color-warning-500)] fill-[var(--color-warning-500)]' : 'text-[var(--color-foreground-muted)]' }}"
                         fill="{{ $archive->is_favorite ? 'currentColor' : 'none' }}"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    <span>{{ $archive->is_favorite ? __('Favorited') : __('Add to Favorites') }}</span>
                </button>
                <span>@lang('Type'): <strong class="text-[var(--color-foreground)]">{{ ucfirst($this->type) }}</strong></span>
                <span>@lang('Created'): <strong class="text-[var(--color-foreground)]">{{ $archive->created_at->format('M j, Y g:i A') }}</strong></span>
                @if ($archive->updated_at !== $archive->created_at)
                    <span>@lang('Updated'): <strong class="text-[var(--color-foreground)]">{{ $archive->updated_at->format('M j, Y g:i A') }}</strong></span>
                @endif
            </div>

            {{-- Tags --}}
            @if ($archive->tags->isNotEmpty())
                <div class="flex flex-wrap gap-1.5 mt-3">
                    @foreach ($archive->tags as $tag)
                        <x-core::badge variant="primary">{{ $tag->name }}</x-core::badge>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Description --}}
        @if ($archive->description)
            <x-core::card class="mb-6">
                <h2 class="text-sm font-semibold text-[var(--color-foreground-muted)] uppercase tracking-wider mb-3">@lang('Description')</h2>
                <div class="prose prose-sm dark:prose-invert max-w-none text-[var(--color-foreground)]">
                    {{ $archive->description }}
                </div>
            </x-core::card>
        @endif

        {{-- Type-Specific Details --}}
        @php
            $extensionRelation = $archive->extension();
            $extension = $extensionRelation ? $extensionRelation->first() : null;
        @endphp
        @if ($extension)
            <x-core::card class="mb-6">
                <h2 class="text-sm font-semibold text-[var(--color-foreground-muted)] uppercase tracking-wider mb-4">{{ ucfirst($this->type) }} @lang('Details')</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @php
                        $fields = $extension->toArray();
                        $hiddenKeys = ['id', 'archive_id', 'created_at', 'updated_at'];
                        $labels = [
                            'url' => 'URL',
                            'domain' => 'Domain',
                            'preview_image' => 'Preview Image',
                            'preview_description' => 'Preview Description',
                            'file_path' => 'File Path',
                            'mime_type' => 'MIME Type',
                            'width' => 'Width',
                            'height' => 'Height',
                            'file_size' => 'File Size',
                            'original_name' => 'Original Name',
                            'alt_text' => 'Alt Text',
                            'due_date' => 'Due Date',
                            'completed_at' => 'Completed At',
                            'priority' => 'Priority',
                            'start_date' => 'Start Date',
                            'end_date' => 'End Date',
                            'status' => 'Status',
                            'progress' => 'Progress',
                            'repository_url' => 'Repository URL',
                            'provider' => 'Provider',
                            'platform' => 'Platform',
                            'completion_status' => 'Completion Status',
                            'author' => 'Author',
                            'isbn' => 'ISBN',
                            'pages' => 'Pages',
                            'started_at' => 'Started At',
                            'finished_at' => 'Finished At',
                            'code_language' => 'Language',
                            'code_content' => 'Code',
                            'source_url' => 'Source URL',
                            'feed_url' => 'Feed URL',
                            'entry_date' => 'Entry Date',
                            'mood' => 'Mood',
                            'location' => 'Location',
                        ];
                    @endphp
                    @foreach ($fields as $key => $value)
                        @if (!in_array($key, $hiddenKeys) && $value !== null && $value !== '')
                            <div class="{{ $key === 'code_content' ? 'sm:col-span-2' : '' }}">
                                <dt class="text-xs font-medium text-[var(--color-foreground-muted)] uppercase tracking-wider">
                                    {{ $labels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}
                                </dt>
                                <dd class="mt-1 text-sm text-[var(--color-foreground)]">
                                    @if ($key === 'url' || $key === 'source_url' || $key === 'feed_url' || $key === 'repository_url')
                                        <a href="{{ $value }}" target="_blank" rel="noopener noreferrer"
                                           class="text-[var(--color-primary-600)] hover:underline break-all">
                                            {{ $value }}
                                        </a>
                                    @elseif ($key === 'priority')
                                        <x-core::badge :variant="match($value) { 'urgent' => 'danger', 'high' => 'warning', 'medium' => 'warning', 'low' => 'success', default => 'secondary' }">
                                            {{ ucfirst($value) }}
                                        </x-core::badge>
                                    @elseif ($key === 'code_content')
                                        <pre class="mt-1 p-4 bg-[var(--color-surface-secondary)] rounded-lg overflow-x-auto text-sm font-mono text-[var(--color-foreground)] border border-[var(--color-border)]"><code>{{ $value }}</code></pre>
                                    @elseif (str_contains($key, '_at') || str_contains($key, '_date') || $key === 'due_date')
                                        {{ \Carbon\Carbon::parse($value)->format('M j, Y') }}
                                    @elseif ($key === 'progress')
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-[var(--color-surface-tertiary)] rounded-full h-2">
                                                <div class="bg-[var(--color-primary-600)] h-2 rounded-full" style="width: {{ $value }}%"></div>
                                            </div>
                                            <span class="text-xs font-medium">{{ $value }}%</span>
                                        </div>
                                    @elseif ($key === 'file_size' && is_numeric($value))
                                        {{ number_format($value / 1024, 1) }} KB
                                    @elseif ($key === 'pages')
                                        {{ number_format($value) }} @lang('pages')
                                    @else
                                        {{ $value }}
                                    @endif
                                </dd>
                            </div>
                        @endif
                    @endforeach
                </dl>
            </x-core::card>
        @endif
    @endif
</div>
