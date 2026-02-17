<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Política de Privacidade - Shava Haux')]
class PrivacyPolicyPage extends Component
{
    public function render()
    {
        return view('livewire.privacy-policy-page');
    }
}
