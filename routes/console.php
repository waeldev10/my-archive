<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Modules\Archives\Console\CleanupTrashedArchives;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('archives:cleanup-trashed', function () {
    $this->call(CleanupTrashedArchives::class);
})->purpose('Permanently delete archives trashed for over 30 days');

Schedule::command('archives:cleanup-trashed')->daily();
