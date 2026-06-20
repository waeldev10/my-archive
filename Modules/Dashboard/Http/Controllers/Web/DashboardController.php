<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Controllers\Web;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index(): View
    {
        return view('dashboard::pages.index');
    }
}
