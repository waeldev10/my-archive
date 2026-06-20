<?php

declare(strict_types=1);

namespace Modules\Archives\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Controller;
use Modules\Archives\Http\Requests\StoreArchiveRequest;
use Modules\Archives\Http\Requests\UpdateArchiveRequest;
use Modules\Archives\Http\Resources\ArchiveResource;
use Modules\Archives\Models\Archive;
use Modules\Archives\Services\ArchiveService;
use Modules\Archives\DTOs\ArchiveData;

class ArchiveController extends Controller
{
    public function __construct(
        private readonly ArchiveService $archiveService,
    ) {}

    /**
     * List archives of a given type.
     *
     * GET /archives/{type}
     */
    public function index(Request $request, string $type): JsonResponse
    {
        $filters = $request->only(['page', 'per_page', 'sort', 'order', 'favorite', 'tag', 'search']);

        $archives = $this->archiveService->list(
            $request->user(),
            $type,
            $filters,
        );

        return response()->json([
            'data' => ArchiveResource::collection($archives),
            'meta' => [
                'current_page' => $archives->currentPage(),
                'last_page' => $archives->lastPage(),
                'per_page' => $archives->perPage(),
                'total' => $archives->total(),
            ],
        ]);
    }

    /**
     * Create a new archive.
     *
     * POST /archives/{type}
     */
    public function store(StoreArchiveRequest $request, string $type): ArchiveResource
    {
        $this->authorize('create', Archive::class);

        $data = new ArchiveData(
            type: $type,
            title: $request->input('title'),
            description: $request->input('description'),
            isFavorite: (bool) $request->input('is_favorite', false),
            tags: $request->input('tags', []),
            typeData: $this->extractTypeData($request, $type),
        );

        $archive = $this->archiveService->create(
            $request->user(),
            $data,
        );

        $archive->load('tags');

        return new ArchiveResource($archive);
    }

    /**
     * Get a single archive.
     *
     * GET /archives/{type}/{archive}
     */
    public function show(Request $request, string $type, string $archive): ArchiveResource|JsonResponse
    {
        $resource = $this->archiveService->find($request->user(), $archive);

        if ($resource === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $this->authorize('view', $resource);

        return new ArchiveResource($resource);
    }

    /**
     * Update an archive.
     *
     * PUT /archives/{type}/{archive}
     */
    public function update(UpdateArchiveRequest $request, string $type, string $archive): ArchiveResource|JsonResponse
    {
        $resource = $this->archiveService->find($request->user(), $archive);

        if ($resource === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $this->authorize('update', $resource);

        $data = new ArchiveData(
            type: $type,
            title: $request->input('title', $resource->title),
            description: $request->input('description', $resource->description),
            isFavorite: (bool) $request->input('is_favorite', $resource->is_favorite),
            tags: $request->input('tags', []),
            typeData: $this->extractTypeData($request, $type),
        );

        $updated = $this->archiveService->update($resource, $data);
        $updated->load('tags');

        return new ArchiveResource($updated);
    }

    /**
     * Delete an archive (soft delete).
     *
     * DELETE /archives/{type}/{archive}
     */
    public function destroy(Request $request, string $type, string $archive): JsonResponse
    {
        $resource = $this->archiveService->find($request->user(), $archive);

        if ($resource === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $this->authorize('delete', $resource);

        $this->archiveService->delete($resource);

        return response()->json(['message' => 'Archive moved to trash.']);
    }

    /**
     * Restore a soft-deleted archive.
     *
     * POST /archives/{type}/{archive}/restore
     */
    public function restore(Request $request, string $type, string $archive): JsonResponse
    {
        $restored = $this->archiveService->restore($request->user(), $archive);

        if ($restored === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $this->authorize('restore', $restored);

        return response()->json(['message' => 'Archive restored.']);
    }

    /**
     * Permanently delete an archive.
     *
     * DELETE /archives/{type}/{archive}/force
     */
    public function forceDelete(Request $request, string $type, string $archive): JsonResponse
    {
        $resource = $this->archiveService->findWithTrashed($request->user(), $archive);

        if ($resource === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $this->authorize('forceDelete', $resource);

        $this->archiveService->forceDelete($resource);

        return response()->json(['message' => 'Archive permanently deleted.']);
    }

    /**
     * Toggle favorite status.
     *
     * POST /archives/{type}/{archive}/favorite
     */
    public function toggleFavorite(Request $request, string $type, string $archive): JsonResponse
    {
        $resource = $this->archiveService->find($request->user(), $archive);

        if ($resource === null) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $this->authorize('toggleFavorite', $resource);

        $result = $this->archiveService->toggleFavorite($resource);

        return response()->json($result);
    }

    /**
     * Extract type-specific field data from the request.
     *
     * @return array<string, mixed>|null
     */
    private function extractTypeData(Request $request, string $type): ?array
    {
        $fields = match ($type) {
            'link' => ['url', 'domain', 'preview_image', 'preview_description'],
            'image' => ['file_path', 'mime_type', 'width', 'height', 'file_size', 'alt_text'],
            'file' => ['file_path', 'mime_type', 'file_size', 'original_name'],
            'todo' => ['due_date', 'completed_at', 'priority'],
            'plan' => ['start_date', 'end_date', 'status', 'progress'],
            'project' => ['start_date', 'end_date', 'status', 'repository_url'],
            'course' => ['provider', 'platform', 'completion_status', 'progress'],
            'book' => ['author', 'isbn', 'pages', 'status', 'started_at', 'finished_at'],
            'snippet' => ['code_language', 'code_content', 'source_url'],
            'website' => ['url', 'domain', 'feed_url'],
            'journal' => ['entry_date', 'mood', 'location'],
            default => [],
        };

        if (empty($fields)) {
            return null;
        }

        $data = [];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $data[$field] = $request->input($field);
            }
        }

        return empty($data) ? null : $data;
    }
}
