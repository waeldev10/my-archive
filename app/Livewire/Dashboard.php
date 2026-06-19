<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    /**
     * Render the dashboard component.
     */
    public function render()
    {
        return view('livewire.dashboard.index')
            ->layout('core::layouts.app');
    }
}
