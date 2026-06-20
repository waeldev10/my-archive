<?php

use Modules\Archives\Providers\ArchiveServiceProvider;
use Modules\Auth\Providers\AuthServiceProvider;
use Modules\Core\Providers\AppServiceProvider;
use Modules\Dashboard\Providers\DashboardServiceProvider;
use Modules\Tags\Providers\TagServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    ArchiveServiceProvider::class,
    DashboardServiceProvider::class,
    TagServiceProvider::class,
];
