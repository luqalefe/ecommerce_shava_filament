<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Log para debug
        Log::info('EditOrder - Dados antes de salvar', [
            'data' => $data,
            'record_id' => $this->record->id,
            'current_status' => $this->record->status,
            'new_status' => $data['status'] ?? null,
        ]);

        return $data;
    }

    protected function afterSave(): void
    {
        // Recarrega o modelo para garantir que está atualizado
        $this->record->refresh();
        
        Log::info('EditOrder - Pedido salvo com sucesso', [
            'order_id' => $this->record->id,
            'status' => $this->record->status,
        ]);
        
        // Envia notificação de sucesso
        \Filament\Notifications\Notification::make()
            ->title('Pedido atualizado com sucesso')
            ->success()
            ->send();
    }
}
