<?php

declare(strict_types=1);

namespace Modules\Archives\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Archives\DTOs\ArchiveData;
use Modules\Archives\Models\Archive;

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
                $this->syncTags($archive, $data->tags);
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
     * Sync tags by name.
     *
     * @param array<int, string> $tagNames
     */
    private function syncTags(Archive $archive, array $tagNames): void
    {
        $tagIds = [];
        foreach ($tagNames as $name) {
            $tag = \Modules\Tags\Models\Tag::firstOrCreate([
                'user_id' => $archive->user_id,
                'name' => trim($name),
            ]);
            $tagIds[] = $tag->id;
        }

        $archive->tags()->sync($tagIds);
    }
}
