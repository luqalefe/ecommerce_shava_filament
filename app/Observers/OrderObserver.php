<?php

namespace App\Observers;

use App\Models\Order;
use App\Notifications\OrderStatusChangedNotification;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Verifica se o status foi alterado
        if ($order->wasChanged('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;
            $userId = $order->user_id;

            Log::info('Status do pedido alterado', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'user_id' => $userId,
            ]);

            // Envia notificação ao cliente de forma assíncrona (não bloqueia o save)
            // Usa dispatch para executar após a resposta HTTP
            dispatch(function () use ($order, $oldStatus, $newStatus, $userId) {
                try {
                    // Busca o pedido novamente para garantir que tem os dados atualizados
                    $orderFresh = \App\Models\Order::with('user')->find($order->id);
                    
                    if ($orderFresh && $orderFresh->user) {
                        // Tenta enviar notificação, mas se falhar, apenas loga o erro
                        try {
                            $orderFresh->user->notify(new OrderStatusChangedNotification($orderFresh, $oldStatus, $newStatus));
                            Log::info('Notificação de status enviada com sucesso', [
                                'order_id' => $orderFresh->id,
                                'user_id' => $orderFresh->user_id,
                                'old_status' => $oldStatus,
                                'new_status' => $newStatus,
                            ]);
                        } catch (\Exception $notifyException) {
                            // Se falhar ao enviar notificação, apenas loga
                            Log::warning('Falha ao enviar notificação de status', [
                                'order_id' => $orderFresh->id,
                                'user_id' => $orderFresh->user_id,
                                'error' => $notifyException->getMessage(),
                            ]);
                        }
                    } else {
                        Log::warning('Usuário não encontrado para o pedido', [
                            'order_id' => $order->id,
                            'user_id' => $userId,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Erro no Observer ao enviar notificação', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            })->afterResponse();
        }
    }
}
