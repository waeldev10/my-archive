<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    {{-- Archive detail view --}}
    @php $archive = $this->archive; @endphp

    @if ($archive === null)
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">Archive not found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">The archive you're looking for doesn't exist or has been deleted.</p>
            <a href="{{ route('archives.list', ['type' => $this->type]) }}"
               wire:navigate
               class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                Back to list
            </a>
        </div>
    @else
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('archives.list', ['type' => $this->type]) }}"
               wire:navigate
               class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to {{ ucfirst($this->type) }}s
            </a>

            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                    <button wire:click="toggleFavorite"
                            class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-7 h-7 {{ $archive->is_favorite ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                             fill="{{ $archive->is_favorite ? 'currentColor' : 'none' }}"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </button>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $archive->title }}</h1>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('archives.edit', ['type' => $this->type, 'archive' => $archive->id]) }}"
                       wire:navigate
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    <button wire:click="delete"
                            wire:confirm="Are you sure you want to move this archive to trash?"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-red-600 bg-white border border-red-300 rounded-lg hover:bg-red-50 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800 dark:hover:bg-red-900/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>

            {{-- Meta --}}
            <div class="flex flex-wrap items-center gap-4 mt-3 text-sm text-gray-500 dark:text-gray-400">
                <span>Type: <strong class="text-gray-700 dark:text-gray-300">{{ ucfirst($this->type) }}</strong></span>
                <span>Created: <strong class="text-gray-700 dark:text-gray-300">{{ $archive->created_at->format('M j, Y g:i A') }}</strong></span>
                @if ($archive->updated_at !== $archive->created_at)
                    <span>Updated: <strong class="text-gray-700 dark:text-gray-300">{{ $archive->updated_at->format('M j, Y g:i A') }}</strong></span>
                @endif
            </div>

            {{-- Tags --}}
            @if ($archive->tags->isNotEmpty())
                <div class="flex flex-wrap gap-1.5 mt-3">
                    @foreach ($archive->tags as $tag)
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Description --}}
        @if ($archive->description)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Description</h2>
                <div class="prose prose-sm dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                    {{ $archive->description }}
                </div>
            </div>
        @endif

        {{-- Type-Specific Details --}}
        @php $extension = $archive->extension()->first(); @endphp
        @if ($extension)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">{{ ucfirst($this->type) }} Details</h2>
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
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ $labels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    @if ($key === 'url' || $key === 'source_url' || $key === 'feed_url' || $key === 'repository_url')
                                        <a href="{{ $value }}" target="_blank" rel="noopener noreferrer"
                                           class="text-indigo-600 dark:text-indigo-400 hover:underline break-all">
                                            {{ $value }}
                                        </a>
                                    @elseif ($key === 'priority')
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full
                                            {{ $value === 'urgent' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                            {{ $value === 'high' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300' : '' }}
                                            {{ $value === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                            {{ $value === 'low' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}">
                                            {{ ucfirst($value) }}
                                        </span>
                                    @elseif ($key === 'code_content')
                                        <pre class="mt-1 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg overflow-x-auto text-sm font-mono text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700"><code>{{ $value }}</code></pre>
                                    @elseif (str_contains($key, '_at') || str_contains($key, '_date') || $key === 'due_date')
                                        {{ \Carbon\Carbon::parse($value)->format('M j, Y') }}
                                    @elseif ($key === 'progress')
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $value }}%"></div>
                                            </div>
                                            <span class="text-xs font-medium">{{ $value }}%</span>
                                        </div>
                                    @elseif ($key === 'file_size' && is_numeric($value))
                                        {{ number_format($value / 1024, 1) }} KB
                                    @elseif ($key === 'pages')
                                        {{ number_format($value) }} pages
                                    @else
                                        {{ $value }}
                                    @endif
                                </dd>
                            </div>
                        @endif
                    @endforeach
                </dl>
            </div>
        @endif

        {{-- Timestamps --}}
        <div class="text-xs text-gray-400 dark:text-gray-500 text-center">
            @if ($archive->deleted_at)
                <span class="text-red-500">Deleted {{ $archive->deleted_at->diffForHumans() }}</span>
            @endif
        </div>
    @endif
</div>
