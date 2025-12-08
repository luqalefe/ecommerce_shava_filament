<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Carregar endereço do PJ se existir
        if ($this->record->user_type === 'pj') {
            $endereco = $this->record->enderecos()->first();
            
            if ($endereco) {
                $data['endereco_cep'] = $endereco->cep;
                $data['endereco_rua'] = $endereco->rua;
                $data['endereco_numero'] = $endereco->numero;
                $data['endereco_complemento'] = $endereco->complemento;
                $data['endereco_cidade'] = $endereco->cidade;
                $data['endereco_estado'] = $endereco->estado;
            }
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Atualizar ou criar endereço para PJ
        $user = $this->record;
        
        if ($user->user_type === 'pj' && ($this->data['endereco_cep'] ?? null)) {
            $endereco = $user->enderecos()->first();
            
            $enderecoData = [
                'cep' => $this->data['endereco_cep'],
                'rua' => $this->data['endereco_rua'],
                'numero' => $this->data['endereco_numero'],
                'complemento' => $this->data['endereco_complemento'] ?? null,
                'cidade' => $this->data['endereco_cidade'],
                'estado' => $this->data['endereco_estado'],
            ];
            
            if ($endereco) {
                $endereco->update($enderecoData);
            } else {
                $user->enderecos()->create($enderecoData);
            }
        }
    }
}
