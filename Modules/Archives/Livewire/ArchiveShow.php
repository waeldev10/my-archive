<?php

declare(strict_types=1);

namespace Modules\Archives\Livewire;

use Livewire\Component;
use Modules\Archives\Models\Archive;
use Modules\Archives\Services\ArchiveService;

class ArchiveShow extends Component
{
    public string $type;

    public string $archiveId;

    /**
     * Get the archive model via service.
     */
    public function getArchiveProperty(): ?Archive
    {
        /** @var ArchiveService $service */
        $service = app(ArchiveService::class);

        return $service->find(auth()->user(), $this->archiveId);
    }

    /**
     * Toggle the favorite status.
     */
    public function toggleFavorite(): void
    {
        $archive = $this->archive;

        if ($archive !== null) {
            /** @var ArchiveService $service */
            $service = app(ArchiveService::class);
            $service->toggleFavorite($archive);
        }
    }

    /**
     * Soft delete the archive.
     */
    public function delete(): void
    {
        $archive = $this->archive;

        if ($archive !== null) {
            /** @var ArchiveService $service */
            $service = app(ArchiveService::class);
            $service->delete($archive);
            session()->flash('success', 'Archive moved to trash.');
            $this->redirect(route('archives.list', ['type' => $this->type]), navigate: true);
        }
    }

    public function render()
    {
        return view('archives::archives.show');
    }
}
