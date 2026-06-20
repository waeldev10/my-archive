<x-core::card>
    <h2 class="text-lg font-semibold text-[var(--color-foreground)] mb-5">{{ ucfirst($this->type) }} @lang('Details')</h2>
    <div class="space-y-5">

    {{-- LINK --}}
    @if ($this->type === 'link')
        <x-core::input label="URL" name="url" type="url" model="url" required placeholder="https://example.com" />
        <x-core::input label="Domain" name="domain" model="domain" placeholder="example.com" />

    {{-- IMAGE --}}
    @elseif ($this->type === 'image')
        <div>
            <label for="file" class="block text-sm font-medium text-[var(--color-foreground)] mb-1">
                @lang('Image File') <span class="text-[var(--color-danger-500)]">*</span>
            </label>
            <input wire:model="file" type="file" id="file" accept="image/jpeg,image/png,image/gif,image/webp"
                   @if($mode === 'create') required @endif
                   class="block w-full text-sm text-[var(--color-foreground-muted)]
                          file:me-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold
                          file:bg-[var(--color-primary-50)] file:text-[var(--color-primary-700)]
                          hover:file:bg-[var(--color-primary-100)]
                          dark:file:bg-[var(--color-primary-950)] dark:file:text-[var(--color-primary-300)] dark:hover:file:bg-[var(--color-primary-900)]">
            @error('file')<p class="mt-1 text-xs text-[var(--color-danger-600)]">{{ $message }}</p>@enderror
        </div>
        <x-core::input label="Alt Text" name="alt_text" model="alt_text" placeholder="Describe the image..." />

    {{-- FILE --}}
    @elseif ($this->type === 'file')
        <div>
            <label for="file" class="block text-sm font-medium text-[var(--color-foreground)] mb-1">
                @lang('File') <span class="text-[var(--color-danger-500)]">*</span>
            </label>
            <input wire:model="file" type="file" id="file"
                   @if($mode === 'create') required @endif
                   class="block w-full text-sm text-[var(--color-foreground-muted)]
                          file:me-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold
                          file:bg-[var(--color-primary-50)] file:text-[var(--color-primary-700)]
                          hover:file:bg-[var(--color-primary-100)]
                          dark:file:bg-[var(--color-primary-950)] dark:file:text-[var(--color-primary-300)] dark:hover:file:bg-[var(--color-primary-900)]">
            @error('file')<p class="mt-1 text-xs text-[var(--color-danger-600)]">{{ $message }}</p>@enderror
        </div>

    {{-- TODO --}}
    @elseif ($this->type === 'todo')
        <x-core::input label="Due Date" name="due_date" type="date" model="due_date" />
        <x-core::select label="Priority" name="priority" model="priority"
            :options="['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent']"
            selected="medium" />

    {{-- PLAN --}}
    @elseif ($this->type === 'plan')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-core::input label="Start Date" name="start_date" type="date" model="start_date" />
            <x-core::input label="End Date" name="end_date" type="date" model="end_date" />
        </div>
        <x-core::select label="Status" name="status" model="status"
            :options="['draft' => 'Draft', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled']"
            selected="draft" />
        <div>
            <label class="block text-sm font-medium text-[var(--color-foreground)] mb-1">@lang('Progress'): {{ $progress }}%</label>
            <input wire:model="progress" type="range" min="0" max="100"
                   class="w-full accent-[var(--color-primary-600)]" aria-label="Progress">
        </div>

    {{-- PROJECT --}}
    @elseif ($this->type === 'project')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-core::input label="Start Date" name="start_date" type="date" model="start_date" />
            <x-core::input label="End Date" name="end_date" type="date" model="end_date" />
        </div>
        <x-core::select label="Status" name="status" model="status"
            :options="['draft' => 'Draft', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled']"
            selected="draft" />
        <x-core::input label="Repository URL" name="repository_url" type="url" model="repository_url" placeholder="https://github.com/user/repo" />

    {{-- COURSE --}}
    @elseif ($this->type === 'course')
        <x-core::input label="Provider" name="provider" model="provider" placeholder="Coursera, Udemy, etc." />
        <x-core::input label="Platform" name="platform" model="platform" placeholder="Online, in-person, etc." />
        <x-core::select label="Completion Status" name="completion_status" model="completion_status"
            :options="['not_started' => 'Not Started', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'dropped' => 'Dropped']"
            selected="not_started" />
        <div>
            <label class="block text-sm font-medium text-[var(--color-foreground)] mb-1">@lang('Progress'): {{ $progress }}%</label>
            <input wire:model="progress" type="range" min="0" max="100"
                   class="w-full accent-[var(--color-primary-600)]" aria-label="Progress">
        </div>

    {{-- BOOK --}}
    @elseif ($this->type === 'book')
        <x-core::input label="Author" name="author" model="author" placeholder="Author name" />
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-core::input label="ISBN" name="isbn" model="isbn" placeholder="978-3-16-148410-0" />
            <x-core::input label="Pages" name="pages" type="number" model="pages" placeholder="Number of pages" />
        </div>
        <x-core::select label="Reading Status" name="book_status" model="book_status"
            :options="['to_read' => 'To Read', 'reading' => 'Reading', 'completed' => 'Completed', 'dropped' => 'Dropped', 'on_hold' => 'On Hold']"
            selected="to_read" />
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-core::input label="Started Reading" name="started_at" type="date" model="started_at" />
            <x-core::input label="Finished Reading" name="finished_at" type="date" model="finished_at" />
        </div>

    {{-- SNIPPET --}}
    @elseif ($this->type === 'snippet')
        <x-core::input label="Language" name="code_language" model="code_language" placeholder="PHP, JavaScript, Python, etc." />
        <x-core::textarea label="Code" name="code_content" model="code_content" rows="10" placeholder="Paste your code here..." />
        <x-core::input label="Source URL" name="source_url" type="url" model="source_url" placeholder="https://github.com/user/repo/blob/main/example.php" />

    {{-- WEBSITE --}}
    @elseif ($this->type === 'website')
        <x-core::input label="URL" name="url" type="url" model="url" required placeholder="https://example.com" />
        <x-core::input label="Domain" name="domain" model="domain" placeholder="example.com" />
        <x-core::input label="Feed URL" name="feed_url" type="url" model="feed_url" placeholder="https://example.com/feed.xml" />

    {{-- JOURNAL --}}
    @elseif ($this->type === 'journal')
        <x-core::input label="Entry Date" name="entry_date" type="date" model="entry_date" />
        <x-core::input label="Mood" name="mood" model="mood" placeholder="Happy, thoughtful, etc." />
        <x-core::input label="Location" name="location" model="location" placeholder="City, country, etc." />
    @endif

    </div>
</x-core::card>
