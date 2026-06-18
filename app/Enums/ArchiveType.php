<?php

declare(strict_types=1);

namespace App\Enums;

enum ArchiveType: string
{
    case Note = 'note';
    case Link = 'link';
    case Article = 'article';
    case Image = 'image';
    case File = 'file';
    case Todo = 'todo';
    case Plan = 'plan';
    case Project = 'project';
    case Idea = 'idea';
    case Bookmark = 'bookmark';
    case Course = 'course';
    case Book = 'book';
    case Snippet = 'snippet';
    case Website = 'website';
    case Journal = 'journal';
    case Prompt = 'prompt';

    /**
     * Get the human-readable label for the archive type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Note => 'Note',
            self::Link => 'Link',
            self::Article => 'Article',
            self::Image => 'Image',
            self::File => 'File',
            self::Todo => 'Todo',
            self::Plan => 'Plan',
            self::Project => 'Project',
            self::Idea => 'Idea',
            self::Bookmark => 'Bookmark',
            self::Course => 'Course',
            self::Book => 'Book',
            self::Snippet => 'Snippet',
            self::Website => 'Website',
            self::Journal => 'Journal',
            self::Prompt => 'Prompt',
        };
    }

    /**
     * Get the extension table associated with this archive type, if any.
     */
    public function extensionTable(): ?string
    {
        return match ($this) {
            self::Link => 'archive_links',
            self::Image => 'archive_images',
            self::File => 'archive_files',
            self::Todo => 'archive_todos',
            self::Plan => 'archive_plans',
            self::Project => 'archive_projects',
            self::Course => 'archive_courses',
            self::Book => 'archive_books',
            self::Snippet => 'archive_snippets',
            self::Website => 'archive_websites',
            self::Journal => 'archive_journals',
            default => null,
        };
    }

    /**
     * Get the model class for the extension table, if any.
     */
    public function extensionModel(): ?string
    {
        return match ($this) {
            self::Link => \App\Models\ArchiveLink::class,
            self::Image => \App\Models\ArchiveImage::class,
            self::File => \App\Models\ArchiveFile::class,
            self::Todo => \App\Models\ArchiveTodo::class,
            self::Plan => \App\Models\ArchivePlan::class,
            self::Project => \App\Models\ArchiveProject::class,
            self::Course => \App\Models\ArchiveCourse::class,
            self::Book => \App\Models\ArchiveBook::class,
            self::Snippet => \App\Models\ArchiveSnippet::class,
            self::Website => \App\Models\ArchiveWebsite::class,
            self::Journal => \App\Models\ArchiveJournal::class,
            default => null,
        };
    }

    /**
     * Get all archive type values as an array of strings.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    /**
     * Check if this type has an extension table.
     */
    public function hasExtensionTable(): bool
    {
        return $this->extensionTable() !== null;
    }
}
