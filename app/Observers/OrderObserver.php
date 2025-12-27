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
            // IMPORTANTE: Este código NUNCA deve lançar exceção para não bloquear o save
            // O Observer estava causando timeout (tela preta) e duplicidade de emails.
            // Desabilitado temporariamente em favor da notificação manual no OrderResource.
            /*
            dispatch(function () use ($order, $oldStatus, $newStatus, $userId) {
                try {
                    // Busca o pedido novamente para garantir que tem os dados atualizados
                    $orderFresh = \App\Models\Order::with('user')->find($order->id);
                    
                    if ($orderFresh && $orderFresh->user) {
                        // Tenta enviar notificação, mas se falhar, apenas loga o erro
                        try {
                            $orderFresh->user->notify(new OrderStatusChangedNotification($orderFresh, $oldStatus, $newStatus));
                            Log::info('Notificação enviada com sucesso', [
                                'order_id' => $orderFresh->id,
                                'user_id' => $orderFresh->user_id,
                            ]);
                        } catch (\Exception $notifyException) {
                            // Se falhar ao enviar notificação (ex: email não configurado), apenas loga
                            // NÃO lança exceção para não bloquear o save do pedido
                            Log::warning('Falha ao enviar notificação (não bloqueia o save)', [
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
                    // Catch geral para garantir que NUNCA bloqueia o save
                    Log::error('Erro no Observer (não bloqueia o save)', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            })->afterResponse();
            */
        }
    }
}
