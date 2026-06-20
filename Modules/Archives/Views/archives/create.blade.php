<div>
    {{-- Error Message --}}
    @if ($errorMessage)
        <div class="mb-6">
            <x-core::alert type="danger" :message="$errorMessage" dismissible />
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        {{-- Common Fields --}}
        <x-core::card>
            <h2 class="text-lg font-semibold text-[var(--color-foreground)] mb-5">@lang('Details')</h2>
            <div class="space-y-5">
                {{-- Title --}}
                <x-core::input
                    label="Title"
                    name="title"
                    model="title"
                    required
                    maxlength="255"
                    placeholder="Enter title..."
                />

                {{-- Description --}}
                <x-core::textarea
                    label="Description"
                    name="description"
                    model="description"
                    rows="4"
                    placeholder="Enter description..."
                />

                {{-- Tags --}}
                <div>
                    <label class="block text-sm font-medium text-[var(--color-foreground)] mb-1">@lang('Tags')</label>
                    <div class="flex flex-wrap gap-2 mb-2" x-data="{}">
                        @foreach ($tags as $index => $tag)
                            <x-core::badge variant="primary" removable wire:key="tag-{{ $tag }}">
                                {{ $tag }}
                                <button type="button" wire:click="removeTag({{ $index }})"
                                        class="text-inherit hover:opacity-70 cursor-pointer">&times;</button>
                            </x-core::badge>
                        @endforeach
                    </div>
                    <div class="flex gap-2" x-data="{ tagInput: '' }">
                        <input x-model="tagInput"
                               type="text"
                               placeholder="Add a tag..."
                               class="flex-1 px-3 py-2 text-sm border border-[var(--color-border)] rounded-lg bg-[var(--color-surface)] text-[var(--color-foreground)] placeholder:text-[var(--color-foreground-muted)] focus:outline-none focus:ring-2 focus:ring-[var(--color-primary-500)] focus:border-transparent"
                               @keydown.enter.prevent="$wire.addTag(tagInput); tagInput = ''">
                        <x-core::button variant="secondary"
                                        x-on:click="$wire.addTag(tagInput); tagInput = ''">
                            @lang('Add')
                        </x-core::button>
                    </div>
                </div>

                {{-- Favorite --}}
                <label class="flex items-center gap-2 text-sm font-medium text-[var(--color-foreground)] cursor-pointer">
                    <input wire:model="is_favorite" type="checkbox"
                           class="rounded border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-primary-600)] focus:ring-[var(--color-primary-500)]">
                    @lang('Mark as favorite')
                </label>
            </div>
        </x-core::card>

        {{-- Type-Specific Fields --}}
        @include('archives::archives.partials.type-fields', ['mode' => 'create'])

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3">
            <x-core::button variant="secondary" :href="route('archives.list', ['type' => $this->type])">
                @lang('Cancel')
            </x-core::button>
            <x-core::button variant="primary" type="submit" loading wire="save">
                <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span wire:loading.remove wire:target="save">@lang('Create :type', ['type' => ucfirst($this->type)])</span>
                <span wire:loading wire:target="save">@lang('Creating...')</span>
            </x-core::button>
        </div>
    </form>
</div>
