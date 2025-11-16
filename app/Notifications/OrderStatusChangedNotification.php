<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification
{
    use Queueable;

    public Order $order;
    public string $oldStatus;
    public string $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, string $oldStatus, string $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database']; // Sempre salva no banco
        
        // Só tenta enviar email se as credenciais estiverem configuradas
        // Verifica se há configuração de email válida
        $mailHost = config('mail.mailers.smtp.host') ?? config('mail.host');
        $mailUser = config('mail.mailers.smtp.username') ?? config('mail.username');
        $mailPass = config('mail.mailers.smtp.password') ?? config('mail.password');
        
        if ($mailHost && $mailUser && $mailPass) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusLabels = [
            'pending' => 'Pendente',
            'processing' => 'Processando',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
        ];

        $newStatusLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;
        $oldStatusLabel = $statusLabels[$this->oldStatus] ?? $this->oldStatus;

        $message = (new MailMessage)
            ->subject('Atualização do Pedido #' . $this->order->id . ' - Shava Haux')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('O status do seu pedido foi atualizado.')
            ->line('**Pedido:** #' . $this->order->id)
            ->line('**Status anterior:** ' . $oldStatusLabel)
            ->line('**Novo status:** ' . $newStatusLabel)
            ->line('**Valor total:** R$ ' . number_format($this->order->total_amount, 2, ',', '.'));

        // Mensagens específicas por status
        match($this->newStatus) {
            'processing' => $message->line('Seu pedido está sendo processado e preparado para envio.'),
            'shipped' => $message->line('Seu pedido foi enviado! Em breve você receberá mais informações sobre a entrega.'),
            'delivered' => $message->line('Seu pedido foi entregue! Esperamos que tenha gostado da sua compra.'),
            'cancelled' => $message->line('Seu pedido foi cancelado. Se você tiver dúvidas, entre em contato conosco.'),
            default => null,
        };

        $message->action('Ver Detalhes do Pedido', route('order.show', $this->order))
            ->line('Obrigado por comprar na Shava Haux!');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $statusLabels = [
            'pending' => 'Pendente',
            'processing' => 'Processando',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
        ];

        $newStatusLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;

        return [
            'order_id' => $this->order->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'new_status_label' => $newStatusLabel,
            'message' => "O status do seu pedido #{$this->order->id} foi alterado para: {$newStatusLabel}",
            'url' => route('order.show', $this->order),
        ];
    }
}
