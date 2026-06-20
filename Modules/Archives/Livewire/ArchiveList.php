<?php

declare(strict_types=1);

namespace Modules\Archives\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Archives\Repositories\ArchiveRepository;
use Modules\Archives\Services\ArchiveService;

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
        /** @var ArchiveService $service */
        $service = app(ArchiveService::class);
        $archive = $service->find(auth()->user(), $id);

        if ($archive !== null) {
            $service->toggleFavorite($archive);
        }
    }

    /**
     * Delete an archive (soft delete).
     */
    public function delete(string $id): void
    {
        /** @var ArchiveService $service */
        $service = app(ArchiveService::class);
        $archive = $service->find(auth()->user(), $id);

        if ($archive !== null) {
            $service->delete($archive);
            session()->flash('success', 'Archive moved to trash.');
        }
    }

    public function render()
    {
        return view('archives::archives.list');
    }
}
