<?php

declare(strict_types=1);

namespace Modules\Archives\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Archives\Models\Archive;
use Modules\Auth\Models\User;

class ArchiveRepository
{
    private const ALLOWED_SORT_COLUMNS = [
        'created_at',
        'updated_at',
        'title',
        'type',
    ];

    /**
     * Paginate archives for a user, filtered by type and optional criteria.
     *
     * @param array<string, mixed> $filters
     */
    public function findByUser(User $user, string $type, array $filters = []): LengthAwarePaginator
    {
        $query = Archive::where('user_id', $user->id)
            ->where('type', $type);

        if (isset($filters['favorite'])) {
            $query->where('is_favorite', filter_var($filters['favorite'], FILTER_VALIDATE_BOOLEAN));
        }

        if (! empty($filters['tag'])) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->where('name', $filters['tag']);
            });
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        $sort = $filters['sort'] ?? 'created_at';
        $order = $filters['order'] ?? 'desc';

        // Only allow sorting by indexed columns to prevent full table scans
        if (! in_array($sort, self::ALLOWED_SORT_COLUMNS, true)) {
            $sort = 'created_at';
        }

        // Validate sort direction
        $order = in_array(strtolower($order), ['asc', 'desc'], true) ? $order : 'desc';

        $perPage = min((int) ($filters['per_page'] ?? 20), 100);

        $query->with('tags')->orderBy($sort, $order);

        return $query->paginate($perPage);
    }

    /**
     * Find trashed (soft-deleted) archives for a user.
     */
    public function findTrashed(User $user, string $type): LengthAwarePaginator
    {
        return Archive::where('user_id', $user->id)
            ->where('type', $type)
            ->onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);
    }

    /**
     * Find archives by tag across all types.
     */
    public function findByTag(User $user, string $tagName, int $perPage = 20): LengthAwarePaginator
    {
        return Archive::where('user_id', $user->id)
            ->whereHas('tags', function ($q) use ($tagName) {
                $q->where('name', $tagName);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Find favorites across all types.
     */
    public function findFavorites(User $user, int $perPage = 20): LengthAwarePaginator
    {
        return Archive::where('user_id', $user->id)
            ->where('is_favorite', true)
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Find an archive by ID with ownership check.
     */
    public function findOwned(User $user, string $id): ?Archive
    {
        return Archive::with('tags')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();
    }

    /**
     * Find an archive by ID with ownership check, including trashed records.
     */
    public function findOwnedWithTrashed(User $user, string $id): ?Archive
    {
        return Archive::withTrashed()
            ->with('tags')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();
    }

    /**
     * Count archives by type for a user.
     *
     * @return array<string, int>
     */
    public function countByType(User $user): array
    {
        return Archive::where('user_id', $user->id)
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }
}
