<?php

declare(strict_types=1);

namespace Modules\Archives\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Archives\DTOs\ArchiveData;
use Modules\Archives\Models\Archive;
use Modules\Archives\Services\ArchiveService;
use Modules\Archives\Services\FileUploadService;

class ArchiveEdit extends Component
{
    use WithFileUploads;

    public string $type;

    public string $archiveId;

    public string $title = '';

    public string $description = '';

    public bool $is_favorite = false;

    /** @var array<string> */
    public array $tags = [];

    // Type-specific fields
    public ?string $url = null;

    public ?string $domain = null;

    public ?string $preview_image = null;

    public ?string $preview_description = null;

    public ?string $alt_text = null;

    public ?string $due_date = null;

    public ?string $completed_at = null;

    public string $priority = 'medium';

    public ?string $start_date = null;

    public ?string $end_date = null;

    public string $status = 'draft';

    public int $progress = 0;

    public ?string $repository_url = null;

    public ?string $provider = null;

    public ?string $platform = null;

    public string $completion_status = 'not_started';

    public ?string $author = null;

    public ?string $isbn = null;

    public ?int $pages = null;

    public string $book_status = 'to_read';

    public ?string $started_at = null;

    public ?string $finished_at = null;

    public ?string $code_language = null;

    public string $code_content = '';

    public ?string $source_url = null;

    public ?string $feed_url = null;

    public ?string $entry_date = null;

    public ?string $mood = null;

    public ?string $location = null;

    public bool $isLoading = false;

    public ?string $errorMessage = null;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $file = null;

    public function mount(string $type): void
    {
        $this->type = $type;

        $this->loadArchive();
    }

    /**
     * Load existing archive data into the form.
     */
    private function loadArchive(): void
    {
        /** @var ArchiveService $service */
        $service = app(ArchiveService::class);
        $archive = $service->find(auth()->user(), $this->archiveId);

        if ($archive === null) {
            abort(404);
        }

        $this->title = $archive->title;
        $this->description = $archive->description ?? '';
        $this->is_favorite = $archive->is_favorite;
        $this->tags = $archive->tags->pluck('name')->toArray();

        // Load type-specific fields from extension
        $extensionRelation = $archive->extension();
        $extension = $extensionRelation ? $extensionRelation->first() : null;
        if ($extension !== null) {
            $extData = $extension->toArray();
            foreach ($extData as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    public function addTag(mixed $value): void
    {
        $tag = is_string($value) ? $value : ($value[0] ?? '');
        $tag = trim($tag);
        if ($tag !== '' && ! in_array($tag, $this->tags, true)) {
            $this->tags[] = $tag;
        }
    }

    public function removeTag(int $index): void
    {
        if (isset($this->tags[$index])) {
            unset($this->tags[$index]);
            $this->tags = array_values($this->tags);
        }
    }

    /**
     * Update the archive.
     */
    public function save(): void
    {
        $this->isLoading = true;
        $this->errorMessage = null;

        try {
            // Validate file upload if present
            if ($this->file) {
                $rules = $this->type === 'image'
                    ? FileUploadService::imageValidationRules()
                    : FileUploadService::fileValidationRules();
                $this->validate($rules);
            }

            /** @var ArchiveService $service */
            $service = app(ArchiveService::class);
            $archive = $service->find(auth()->user(), $this->archiveId);

            if ($archive === null) {
                abort(404);
            }

            $service->update(
                $archive,
                new ArchiveData(
                    type: $this->type,
                    title: $this->title,
                    description: $this->description ?: null,
                    isFavorite: $this->is_favorite,
                    tags: array_filter(array_map('trim', $this->tags)),
                    typeData: $this->getTypeData(),
                ),
            );

            // Handle file upload after update
            $this->handleFileUpload($archive->fresh());

            session()->flash('success', ucfirst($this->type) . ' updated successfully.');

            $this->redirect(route('archives.show', ['type' => $this->type, 'archive' => $this->archiveId]), navigate: true);
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->isLoading = false;
    }

    /**
     * Get the type-specific fields as an array.
     *
     * @return array<string, mixed>|null
     */
    private function getTypeData(): ?array
    {
        return match ($this->type) {
            'link' => array_filter([
                'url' => $this->url,
                'domain' => $this->domain,
                'preview_image' => $this->preview_image,
                'preview_description' => $this->preview_description,
            ], fn ($v) => $v !== null),
            'todo' => array_filter([
                'due_date' => $this->due_date,
                'completed_at' => $this->completed_at,
                'priority' => $this->priority,
            ], fn ($v) => $v !== null),
            'plan' => array_filter([
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'status' => $this->status,
                'progress' => $this->progress,
            ], fn ($v) => $v !== null && $v !== ''),
            'project' => array_filter([
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'status' => $this->status,
                'repository_url' => $this->repository_url,
            ], fn ($v) => $v !== null && $v !== ''),
            'course' => array_filter([
                'provider' => $this->provider,
                'platform' => $this->platform,
                'completion_status' => $this->completion_status,
                'progress' => $this->progress,
            ], fn ($v) => $v !== null && $v !== ''),
            'book' => array_filter([
                'author' => $this->author,
                'isbn' => $this->isbn,
                'pages' => $this->pages,
                'status' => $this->book_status,
                'started_at' => $this->started_at,
                'finished_at' => $this->finished_at,
            ], fn ($v) => $v !== null && $v !== ''),
            'snippet' => array_filter([
                'code_language' => $this->code_language,
                'code_content' => $this->code_content,
                'source_url' => $this->source_url,
            ], fn ($v) => $v !== null && $v !== ''),
            'website' => array_filter([
                'url' => $this->url,
                'domain' => $this->domain,
                'feed_url' => $this->feed_url,
            ], fn ($v) => $v !== null && $v !== ''),
            'journal' => array_filter([
                'entry_date' => $this->entry_date,
                'mood' => $this->mood,
                'location' => $this->location,
            ], fn ($v) => $v !== null && $v !== ''),
            'image' => array_filter([
                'alt_text' => $this->alt_text,
            ], fn ($v) => $v !== null),
            'file' => [],
            default => null,
        };
    }

    /**
     * Upload the file and update the extension record with metadata.
     * Deletes the old file before uploading a replacement.
     */
    private function handleFileUpload(Archive $archive): void
    {
        if ($this->file === null) {
            return;
        }

        /** @var FileUploadService $service */
        $service = app(FileUploadService::class);
        $user = auth()->user();
        $extension = $archive->extension()->first();

        if ($extension === null) {
            return;
        }

        // Delete old file if it exists
        if (! empty($extension->file_path)) {
            $service->delete($extension->file_path);
        }

        $path = match ($this->type) {
            'image' => $service->uploadImage($this->file, $user, $archive),
            'file' => $service->uploadFile($this->file, $user, $archive),
            default => null,
        };

        if ($path !== null) {
            $updateData = [
                'file_path' => $path,
                'mime_type' => $this->file->getMimeType(),
                'file_size' => $this->file->getSize(),
            ];

            if ($this->type === 'file') {
                $updateData['original_name'] = $this->file->getClientOriginalName();
            }

            $extension->update($updateData);
        }
    }

    public function render()
    {
        return view('archives::archives.edit');
    }
}
