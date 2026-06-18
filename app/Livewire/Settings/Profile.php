<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use Livewire\Component;

class Profile extends Component
{
    /**
     * Render the profile settings component.
     */
    public function render()
    {
        return view('livewire.settings.profile')
            ->layout('layouts.app');
    }
}
