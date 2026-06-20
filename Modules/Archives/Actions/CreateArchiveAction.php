<?php

declare(strict_types=1);

namespace Modules\Archives\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Archives\DTOs\ArchiveData;
use Modules\Archives\Models\Archive;
use Modules\Tags\Models\Tag;

class CreateArchiveAction
{
    /**
     * Create a new archive with its extension table entry.
     */
    public function execute(string $userId, ArchiveData $data): Archive
    {
        return DB::transaction(function () use ($userId, $data): Archive {
            $archive = Archive::create([
                'user_id' => $userId,
                'type' => $data->type,
                'title' => $data->title,
                'description' => $data->description,
                'is_favorite' => $data->isFavorite,
            ]);

            $this->createExtension($archive, $data);

            if (! empty($data->tags)) {
                $this->syncTags($archive, $userId, $data->tags);
            }

            return $archive;
        });
    }

    /**
     * Create the type-specific extension record.
     */
    private function createExtension(Archive $archive, ArchiveData $data): void
    {
        if ($data->typeData === null) {
            return;
        }

        $extension = $archive->extension();

        if ($extension === null) {
            return;
        }

        $extensionModel = $archive->extension()->getRelated();

        $extensionModel::create(array_merge(
            ['id' => $archive->id],
            $data->typeData,
        ));
    }

    /**
     * Sync tags by name, using bulk operations to avoid N+1.
     *
     * @param array<int, string> $tagNames
     */
    private function syncTags(Archive $archive, string $userId, array $tagNames): void
    {
        $uniqueNames = array_unique(array_map('trim', $tagNames));
        $tagIds = [];

        // Bulk-load existing tags for this user to avoid N SELECTs
        $existing = Tag::where('user_id', $userId)
            ->whereIn('name', $uniqueNames)
            ->get()
            ->keyBy('name');

        foreach ($uniqueNames as $name) {
            if ($existing->has($name)) {
                $tagIds[] = $existing->get($name)->id;
            } else {
                $tag = Tag::create([
                    'user_id' => $userId,
                    'name' => $name,
                ]);
                $tagIds[] = $tag->id;
            }
        }

        $archive->tags()->sync($tagIds);
    }
}
