<?php

declare(strict_types=1);

namespace Modules\Archives\Livewire;

use Livewire\Component;
use Modules\Archives\Models\Archive;

class ArchiveShow extends Component
{
    public string $type;

    public string $archiveId;

    public function mount(string $type, string $archive): void
    {
        $this->type = $type;
        $this->archiveId = $archive;
    }

    /**
     * Get the archive model.
     */
    public function getArchiveProperty(): ?Archive
    {
        return Archive::where('id', $this->archiveId)
            ->where('user_id', auth()->id())
            ->with('tags')
            ->first();
    }

    /**
     * Toggle the favorite status.
     */
    public function toggleFavorite(): void
    {
        $archive = $this->archive;

        if ($archive !== null) {
            $archive->update(['is_favorite' => ! $archive->is_favorite]);
        }
    }

    /**
     * Soft delete the archive.
     */
    public function delete(): void
    {
        $archive = $this->archive;

        if ($archive !== null) {
            $archive->delete();
            session()->flash('success', 'Archive moved to trash.');
            $this->redirect(route('archives.list', ['type' => $this->type]), navigate: true);
        }
    }

    public function render()
    {
        return view('archives::archives.show')
            ->layout('core::layouts.app');
    }
}
