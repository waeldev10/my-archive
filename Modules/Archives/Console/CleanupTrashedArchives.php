<?php

declare(strict_types=1);

namespace Modules\Archives\Console;

use Illuminate\Console\Command;
use Modules\Archives\Models\Archive;

class CleanupTrashedArchives extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'archives:cleanup-trashed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete archives that have been trashed for more than 30 days.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cutoff = now()->subDays(30);

        $archives = Archive::onlyTrashed()
            ->where('deleted_at', '<', $cutoff)
            ->get();

        $count = $archives->count();

        foreach ($archives as $archive) {
            $archive->forceDelete();
        }

        $this->info("Permanently deleted {$count} archived entries that were trashed for over 30 days.");

        return self::SUCCESS;
    }
}
