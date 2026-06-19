<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Livewire Layout
    |--------------------------------------------------------------------------
    |
    | The default layout view that Livewire full-page components use when
    | rendering. This is typically an app shell with navigation and theme
    | support.
    |
    */

    'layout' => 'core::layouts.app',

    /*
    |--------------------------------------------------------------------------
    | Asset URL
    |--------------------------------------------------------------------------
    |
    | The URL for Livewire JavaScript assets. When using Vite with Laravel,
    | this should match your Vite dev server URL or build path.
    |
    */

    'asset_url' => null,

    /*
    |--------------------------------------------------------------------------
    | App URL
    |--------------------------------------------------------------------------
    |
    | This value is used for generating Livewire asset URLs when the app URL
    | differs from the asset URL (e.g., behind a CDN or load balancer).
    |
    */

    'app_url' => null,

];
