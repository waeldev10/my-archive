<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class PageController extends Controller
{
    /**
     * Show the welcome/home page.
     */
    public function welcome(): View
    {
        return view('welcome');
    }
}
