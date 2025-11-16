<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class AboutPage extends Component
{
    public function render()
    {
        return view('livewire.about-page');
    }
}
