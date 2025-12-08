<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        // Se for Pessoa Jurídica e tiver dados de endereço, criar o endereço
        $user = $this->record;
        
        if ($user->user_type === 'pj' && $this->data['endereco_cep'] ?? null) {
            $user->enderecos()->create([
                'cep' => $this->data['endereco_cep'],
                'rua' => $this->data['endereco_rua'],
                'numero' => $this->data['endereco_numero'],
                'complemento' => $this->data['endereco_complemento'] ?? null,
                'cidade' => $this->data['endereco_cidade'],
                'estado' => $this->data['endereco_estado'],
            ]);
        }
    }
}
