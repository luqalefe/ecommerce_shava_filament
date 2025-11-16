<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class MyOrdersList extends Component
{
    use WithPagination;

    public function markAsRead($notificationId)
    {
        Auth::user()->notifications()->where('id', $notificationId)->update(['read_at' => now()]);
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        $orders = Auth::user()
            ->orders()
            ->with(['items.product', 'endereco'])
            ->latest()
            ->paginate(10);

        $notifications = Auth::user()->notifications()->latest()->take(5)->get();
        $unreadCount = Auth::user()->unreadNotifications->count();

        return view('livewire.my-orders-list', [
            'orders' => $orders,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
