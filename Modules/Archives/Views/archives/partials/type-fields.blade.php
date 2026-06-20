<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-5">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ ucfirst($this->type) }} Details</h2>

    {{-- LINK --}}
    @if ($this->type === 'link')
        <div>
            <label for="url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL <span class="text-red-500">*</span></label>
            <input wire:model="url" type="url" id="url" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="https://example.com">
            @error('url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Domain</label>
            <input wire:model="domain" type="text" id="domain" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="example.com">
        </div>

    {{-- IMAGE --}}
    @elseif ($this->type === 'image')
        <div>
            <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Image File <span class="text-red-500">*</span></label>
            <input wire:model="file" type="file" id="file" accept="image/jpeg,image/png,image/gif,image/webp" required
                   class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/50 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/70">
            @error('file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="alt_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alt Text</label>
            <input wire:model="alt_text" type="text" id="alt_text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Describe the image...">
        </div>

    {{-- FILE --}}
    @elseif ($this->type === 'file')
        <div>
            <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">File <span class="text-red-500">*</span></label>
            <input wire:model="file" type="file" id="file" required
                   class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/50 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/70">
            @error('file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

    {{-- TODO --}}
    @elseif ($this->type === 'todo')
        <div>
            <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date</label>
            <input wire:model="due_date" type="date" id="due_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
        </div>
        <div>
            <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
            <select wire:model="priority" id="priority" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
            </select>
        </div>

    {{-- PLAN --}}
    @elseif ($this->type === 'plan')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                <input wire:model="start_date" type="date" id="start_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                <input wire:model="end_date" type="date" id="end_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
            <select wire:model="status" id="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="draft">Draft</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div>
            <label for="progress" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Progress: {{ $progress }}%</label>
            <input wire:model="progress" type="range" id="progress" min="0" max="100" class="w-full accent-indigo-600">
        </div>

    {{-- PROJECT --}}
    @elseif ($this->type === 'project')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                <input wire:model="start_date" type="date" id="start_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                <input wire:model="end_date" type="date" id="end_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
            <select wire:model="status" id="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="draft">Draft</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div>
            <label for="repository_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Repository URL</label>
            <input wire:model="repository_url" type="url" id="repository_url" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="https://github.com/user/repo">
        </div>

    {{-- COURSE --}}
    @elseif ($this->type === 'course')
        <div>
            <label for="provider" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Provider</label>
            <input wire:model="provider" type="text" id="provider" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Coursera, Udemy, etc.">
        </div>
        <div>
            <label for="platform" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Platform</label>
            <input wire:model="platform" type="text" id="platform" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Online, in-person, etc.">
        </div>
        <div>
            <label for="completion_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Completion Status</label>
            <select wire:model="completion_status" id="completion_status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="not_started">Not Started</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="dropped">Dropped</option>
            </select>
        </div>
        <div>
            <label for="progress" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Progress: {{ $progress }}%</label>
            <input wire:model="progress" type="range" id="progress" min="0" max="100" class="w-full accent-indigo-600">
        </div>

    {{-- BOOK --}}
    @elseif ($this->type === 'book')
        <div>
            <label for="author" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Author</label>
            <input wire:model="author" type="text" id="author" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Author name">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="isbn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ISBN</label>
                <input wire:model="isbn" type="text" id="isbn" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="978-3-16-148410-0">
            </div>
            <div>
                <label for="pages" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pages</label>
                <input wire:model="pages" type="number" id="pages" min="1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Number of pages">
            </div>
        </div>
        <div>
            <label for="book_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reading Status</label>
            <select wire:model="book_status" id="book_status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="to_read">To Read</option>
                <option value="reading">Reading</option>
                <option value="completed">Completed</option>
                <option value="dropped">Dropped</option>
                <option value="on_hold">On Hold</option>
            </select>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="started_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Started Reading</label>
                <input wire:model="started_at" type="date" id="started_at" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <div>
                <label for="finished_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Finished Reading</label>
                <input wire:model="finished_at" type="date" id="finished_at" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
        </div>

    {{-- SNIPPET --}}
    @elseif ($this->type === 'snippet')
        <div>
            <label for="code_language" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Language</label>
            <input wire:model="code_language" type="text" id="code_language" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="PHP, JavaScript, Python, etc.">
        </div>
        <div>
            <label for="code_content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Code</label>
            <textarea wire:model="code_content" id="code_content" rows="10" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-mono text-sm placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Paste your code here..." spellcheck="false"></textarea>
        </div>
        <div>
            <label for="source_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Source URL</label>
            <input wire:model="source_url" type="url" id="source_url" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="https://github.com/user/repo/blob/main/example.php">
        </div>

    {{-- WEBSITE --}}
    @elseif ($this->type === 'website')
        <div>
            <label for="url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL <span class="text-red-500">*</span></label>
            <input wire:model="url" type="url" id="url" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="https://example.com">
        </div>
        <div>
            <label for="domain" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Domain</label>
            <input wire:model="domain" type="text" id="domain" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="example.com">
        </div>
        <div>
            <label for="feed_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Feed URL</label>
            <input wire:model="feed_url" type="url" id="feed_url" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="https://example.com/feed.xml">
        </div>

    {{-- JOURNAL --}}
    @elseif ($this->type === 'journal')
        <div>
            <label for="entry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Entry Date</label>
            <input wire:model="entry_date" type="date" id="entry_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
        </div>
        <div>
            <label for="mood" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mood</label>
            <input wire:model="mood" type="text" id="mood" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Happy, thoughtful, etc.">
        </div>
        <div>
            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
            <input wire:model="location" type="text" id="location" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="City, country, etc.">
        </div>
    @endif

    {{-- Note / Article / Idea / Bookmark / Prompt -- simple types with no extension fields --}}
</div>
