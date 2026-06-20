<?php

declare(strict_types=1);

namespace Modules\Archives\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Archives\DTOs\ArchiveData;
use Modules\Archives\Models\Archive;

class UpdateArchiveAction
{
    /**
     * Update an archive and its extension table entry.
     */
    public function execute(Archive $archive, ArchiveData $data): Archive
    {
        return DB::transaction(function () use ($archive, $data): Archive {
            $archive->update([
                'title' => $data->title,
                'description' => $data->description ?? $archive->description,
                'is_favorite' => $data->isFavorite,
            ]);

            $this->updateExtension($archive, $data);

            if (! empty($data->tags)) {
                $this->syncTags($archive, $data->tags);
            }

            return $archive->fresh();
        });
    }

    /**
     * Update the type-specific extension record.
     */
    private function updateExtension(Archive $archive, ArchiveData $data): void
    {
        if ($data->typeData === null) {
            return;
        }

        $extension = $archive->extension()->first();

        if ($extension === null) {
            return;
        }

        $extension->update($data->typeData);
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
