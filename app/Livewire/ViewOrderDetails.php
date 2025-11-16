<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ViewOrderDetails extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        // Verificar se o pedido pertence ao usuário logado
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para visualizar este pedido.');
        }

        // Carregar relacionamentos
        $order->load(['items.product', 'endereco', 'user']);
    }

    public function getStatusSteps()
    {
        $steps = [
            'pending' => ['label' => 'Pendente', 'icon' => 'clock'],
            'processing' => ['label' => 'Processando', 'icon' => 'cog'],
            'shipped' => ['label' => 'Enviado', 'icon' => 'truck'],
            'delivered' => ['label' => 'Entregue', 'icon' => 'check-circle'],
        ];

        $currentStatus = $this->order->status;
        $statusOrder = ['pending', 'processing', 'shipped', 'delivered'];
        
        $result = [];
        foreach ($statusOrder as $index => $status) {
            $isActive = false;
            $isCompleted = false;

            if ($currentStatus === 'cancelled') {
                // Se cancelado, nenhum status está ativo ou completo
                $isActive = false;
                $isCompleted = false;
            } else {
                $currentIndex = array_search($currentStatus, $statusOrder);
                if ($currentIndex !== false) {
                    $isCompleted = $index < $currentIndex;
                    $isActive = $index === $currentIndex;
                }
            }

            $result[] = [
                'status' => $status,
                'label' => $steps[$status]['label'],
                'icon' => $steps[$status]['icon'],
                'isActive' => $isActive,
                'isCompleted' => $isCompleted,
            ];
        }

        return $result;
    }

    public function getStatusBadgeClass()
    {
        return match($this->order->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'shipped' => 'bg-indigo-100 text-indigo-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabel()
    {
        return match($this->order->status) {
            'pending' => 'Pendente',
            'processing' => 'Processando',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
            default => $this->order->status,
        };
    }

    public function render()
    {
        return view('livewire.view-order-details');
    }
}
