<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('archives.show', ['type' => $this->type, 'archive' => $this->archiveId]) }}"
           wire:navigate
           class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to {{ ucfirst($this->type) }}
        </a>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Edit {{ ucfirst($this->type) }}
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Update your archive entry
        </p>
    </div>

    {{-- Error Message --}}
    @if ($errorMessage)
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-red-700 dark:text-red-300">{{ $errorMessage }}</p>
            </div>
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        {{-- Common Fields --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Details</h2>

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Title <span class="text-red-500">*</span>
                </label>
                <input wire:model="title"
                       type="text"
                       id="title"
                       required
                       maxlength="255"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="Enter title...">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Description
                </label>
                <textarea wire:model="description"
                          id="description"
                          rows="4"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                          placeholder="Enter description..."></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tags --}}
            <div>
                <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Tags
                </label>
                <div class="flex flex-wrap gap-2 mb-2">
                    @foreach ($tags as $index => $tag)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                            {{ $tag }}
                            <button type="button" wire:click="removeTag({{ $index }})"
                                    class="text-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-200">&times;</button>
                        </span>
                    @endforeach
                </div>
                <div class="flex gap-2">
                    <input wire:model="newTag"
                           type="text"
                           id="tags-input"
                           placeholder="Add a tag..."
                           class="flex-1 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           @@keydown.enter.prevent="$wire.addTag($event.target.value); $event.target.value = ''">
                    <button type="button"
                            wire:click="addTag(document.getElementById('tags-input').value); document.getElementById('tags-input').value = ''"
                            class="px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                        Add
                    </button>
                </div>
                @error('tags')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Favorite --}}
            <div class="flex items-center gap-2">
                <input wire:model="is_favorite"
                       type="checkbox"
                       id="is_favorite"
                       class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                <label for="is_favorite" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Mark as favorite
                </label>
            </div>
        </div>

        {{-- Type-Specific Fields --}}
        @include('archives::archives.partials.type-fields', ['mode' => 'edit'])

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('archives.show', ['type' => $this->type, 'archive' => $this->archiveId]) }}"
               wire:navigate
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                Cancel
            </a>
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg wire:loading wire:target="save"
                     class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span wire:loading.remove wire:target="save">Update {{ ucfirst($this->type) }}</span>
                <span wire:loading wire:target="save">Updating...</span>
            </button>
        </div>
    </form>
</div>
