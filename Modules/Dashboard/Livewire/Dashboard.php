<?php

declare(strict_types=1);

namespace Modules\Dashboard\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    /**
     * Render the dashboard component.
     */
    public function render()
    {
        return view('dashboard::dashboard.index');
    }
}
