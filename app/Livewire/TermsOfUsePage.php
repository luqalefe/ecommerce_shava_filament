<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Termos de Uso - Shava Haux')]
class TermsOfUsePage extends Component
{
    public function render()
    {
        return view('livewire.terms-of-use-page');
    }
}
