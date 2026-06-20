<?php

declare(strict_types=1);

namespace Modules\Archives\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Archives\Repositories\ArchiveRepository;
use Modules\Archives\Services\ArchiveFactory;

class ArchiveList extends Component
{
    use WithPagination;

    public string $type;

    public string $search = '';

    public string $sort = 'created_at';

    public string $order = 'desc';

    public ?bool $favorite = null;

    /** @var array<string, string> */
    protected $queryString = [
        'search' => ['except' => ''],
        'sort' => ['except' => 'created_at'],
        'order' => ['except' => 'desc'],
        'favorite' => ['except' => null],
    ];

    public function mount(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Reset pagination when search changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Load archives for the current type.
     */
    public function getArchivesProperty(): LengthAwarePaginator
    {
        /** @var \Modules\Auth\Models\User $user */
        $user = auth()->user();

        /** @var ArchiveRepository $repository */
        $repository = app(ArchiveRepository::class);

        $filters = [
            'search' => $this->search,
            'sort' => $this->sort,
            'order' => $this->order,
            'per_page' => 20,
        ];

        if ($this->favorite !== null) {
            $filters['favorite'] = $this->favorite;
        }

        return $repository->findByUser($user, $this->type, $filters);
    }

    public function getTypeLabelProperty(): string
    {
        return ucfirst($this->type);
    }

    /**
     * Toggle favorite status on an archive.
     */
    public function toggleFavorite(string $id): void
    {
        /** @var \Modules\Archives\Repositories\ArchiveRepository $repository */
        $repository = app(ArchiveRepository::class);
        $archive = $repository->findOwned(auth()->user(), $id);

        if ($archive !== null) {
            $archive->update(['is_favorite' => ! $archive->is_favorite]);
        }
    }

    /**
     * Delete an archive (soft delete).
     */
    public function delete(string $id): void
    {
        /** @var \Modules\Archives\Repositories\ArchiveRepository $repository */
        $repository = app(ArchiveRepository::class);
        $archive = $repository->findOwned(auth()->user(), $id);

        if ($archive !== null) {
            $archive->delete();
            session()->flash('success', 'Archive moved to trash.');
        }
    }

    public function render()
    {
        return view('archives::archives.list')
            ->layout('core::layouts.app');
    }
}
