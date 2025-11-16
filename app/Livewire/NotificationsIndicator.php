<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationsIndicator extends Component
{
    public function render()
    {
        $unreadCount = Auth::check() ? Auth::user()->unreadNotifications->count() : 0;
        
        return view('livewire.notifications-indicator', [
            'unreadCount' => $unreadCount,
        ]);
    }
}
