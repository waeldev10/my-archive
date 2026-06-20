<?php

declare(strict_types=1);

namespace Modules\Archives\Services;

use Modules\Archives\Models\Archive;
use Modules\Archives\Models\ArchiveLink;
use Modules\Archives\Models\ArchiveImage;
use Modules\Archives\Models\ArchiveFile;
use Modules\Archives\Models\ArchiveTodo;
use Modules\Archives\Models\ArchivePlan;
use Modules\Archives\Models\ArchiveProject;
use Modules\Archives\Models\ArchiveCourse;
use Modules\Archives\Models\ArchiveBook;
use Modules\Archives\Models\ArchiveSnippet;
use Modules\Archives\Models\ArchiveWebsite;
use Modules\Archives\Models\ArchiveJournal;

class ArchiveFactory
{
    /**
     * Get the model class for the given archive type.
     *
     * @return class-string|null
     */
    public function modelClass(string $type): ?string
    {
        return match ($type) {
            'link' => ArchiveLink::class,
            'image' => ArchiveImage::class,
            'file' => ArchiveFile::class,
            'todo' => ArchiveTodo::class,
            'plan' => ArchivePlan::class,
            'project' => ArchiveProject::class,
            'course' => ArchiveCourse::class,
            'book' => ArchiveBook::class,
            'snippet' => ArchiveSnippet::class,
            'website' => ArchiveWebsite::class,
            'journal' => ArchiveJournal::class,
            default => null,
        };
    }

    /**
     * Check if the given archive type has an extension table/model.
     */
    public function hasExtension(string $type): bool
    {
        return $this->modelClass($type) !== null;
    }

    /**
     * Get all available archive types.
     *
     * @return array<string>
     */
    public function allTypes(): array
    {
        return [
            'note', 'link', 'article', 'image', 'file',
            'todo', 'plan', 'project', 'idea', 'bookmark',
            'course', 'book', 'snippet', 'website', 'journal', 'prompt',
        ];
    }

    /**
     * Get the types that have extension tables.
     *
     * @return array<string>
     */
    public function typesWithExtension(): array
    {
        return ['link', 'image', 'file', 'todo', 'plan', 'project',
            'course', 'book', 'snippet', 'website', 'journal'];
    }

    /**
     * Get the types without extension tables (content in description field).
     *
     * @return array<string>
     */
    public function simpleTypes(): array
    {
        return ['note', 'article', 'idea', 'bookmark', 'prompt'];
    }
}
