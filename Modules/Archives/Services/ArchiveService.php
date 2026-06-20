<?php

declare(strict_types=1);

namespace Modules\Archives\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Archives\Actions\CreateArchiveAction;
use Modules\Archives\Actions\UpdateArchiveAction;
use Modules\Archives\DTOs\ArchiveData;
use Modules\Archives\Models\Archive;
use Modules\Archives\Repositories\ArchiveRepository;
use Modules\Auth\Models\User;

class ArchiveService
{
    public function __construct(
        private readonly ArchiveRepository $repository,
        private readonly CreateArchiveAction $createAction,
        private readonly UpdateArchiveAction $updateAction,
        private readonly ArchiveFactory $factory,
        private readonly FileUploadService $fileUploadService,
    ) {}

    /**
     * List archives of a given type.
     *
     * @param array<string, mixed> $filters
     */
    public function list(User $user, string $type, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->findByUser($user, $type, $filters);
    }

    /**
     * Find a single archive by ID.
     */
    public function find(User $user, string $id): ?Archive
    {
        return $this->repository->findOwned($user, $id);
    }

    /**
     * Find a single archive by ID, including trashed.
     */
    public function findWithTrashed(User $user, string $id): ?Archive
    {
        return $this->repository->findOwnedWithTrashed($user, $id);
    }

    /**
     * Create a new archive.
     */
    public function create(User $user, ArchiveData $data): Archive
    {
        return $this->createAction->execute($user->id, $data);
    }

    /**
     * Update an existing archive.
     */
    public function update(Archive $archive, ArchiveData $data): Archive
    {
        return $this->updateAction->execute($archive, $data);
    }

    /**
     * Soft-delete an archive.
     */
    public function delete(Archive $archive): void
    {
        $archive->delete();
    }

    /**
     * Restore a soft-deleted archive.
     */
    public function restore(User $user, string $id): ?Archive
    {
        $archive = Archive::onlyTrashed()
            ->where('user_id', $user->id)
            ->find($id);

        if ($archive === null) {
            return null;
        }

        $archive->restore();

        return $archive;
    }

    /**
     * Permanently delete an archive and its extension.
     */
    public function forceDelete(Archive $archive): void
    {
        $relation = $archive->extension();
        if ($relation !== null) {
            $extension = $relation->first();
            if ($extension !== null) {
                $extension->delete();
            }
        }

        $archive->tags()->detach();
        $archive->forceDelete();
    }

    /**
     * Toggle the favorite status of an archive.
     *
     * @return array{id: string, is_favorite: bool}
     */
    public function toggleFavorite(Archive $archive): array
    {
        $archive->update([
            'is_favorite' => ! $archive->is_favorite,
        ]);

        return [
            'id' => $archive->id,
            'is_favorite' => $archive->fresh()->is_favorite,
        ];
    }

    /**
     * Find trashed archives.
     */
    public function trashed(User $user, string $type): LengthAwarePaginator
    {
        return $this->repository->findTrashed($user, $type);
    }
}
