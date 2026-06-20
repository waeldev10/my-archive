<?php

declare(strict_types=1);

namespace Modules\Archives\Observers;

use Modules\Archives\Models\Archive;
use Modules\Dashboard\Models\ActivityLog;

/**
 * Observer for the Archive model.
 *
 * Records user activity in the activity_logs table whenever an archive
 * is created, updated, deleted, restored, or its favorite status changes.
 */
class ArchiveObserver
{
    /**
     * Handle the Archive "created" event.
     */
    public function created(Archive $archive): void
    {
        $this->log($archive, 'created');
    }

    /**
     * Handle the Archive "updated" event.
     *
     * Detects favorite toggles and logs them distinctly from regular updates.
     */
    public function updated(Archive $archive): void
    {
        if ($archive->isDirty('is_favorite')) {
            $action = $archive->is_favorite ? 'favorited' : 'unfavorited';
            $this->log($archive, $action);
        } else {
            $this->log($archive, 'updated');
        }
    }

    /**
     * Handle the Archive "deleting" event.
     *
     * Uses "deleting" (before the SQL delete) rather than "deleted" (after)
     * so the ActivityLog can reference the archive ID via FK before the row
     * is removed. On force-delete the FK's ON DELETE SET NULL clears the
     * reference automatically. On soft-delete the archive row persists.
     */
    public function deleting(Archive $archive): void
    {
        $this->log($archive, 'deleted');
    }

    /**
     * Handle the Archive "restored" event.
     */
    public function restored(Archive $archive): void
    {
        $this->log($archive, 'restored');
    }

    /**
     * Create an activity log entry.
     */
    private function log(Archive $archive, string $action): void
    {
        ActivityLog::create([
            'user_id' => $archive->user_id,
            'archive_id' => $archive->id,
            'action' => $action,
            'description' => $this->buildDescription($archive, $action),
        ]);
    }

    /**
     * Build a human-readable description for the activity log entry.
     */
    private function buildDescription(Archive $archive, string $action): string
    {
        $type = ucfirst($archive->type);

        return match ($action) {
            'created' => "{$type} \"{$archive->title}\" was created.",
            'updated' => "{$type} \"{$archive->title}\" was updated.",
            'deleted' => "{$type} \"{$archive->title}\" was moved to trash.",
            'restored' => "{$type} \"{$archive->title}\" was restored from trash.",
            'favorited' => "{$type} \"{$archive->title}\" was added to favorites.",
            'unfavorited' => "{$type} \"{$archive->title}\" was removed from favorites.",
            default => "{$type} \"{$archive->title}\" was {$action}.",
        };
    }
}
