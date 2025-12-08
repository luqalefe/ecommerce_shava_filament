<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('filter_all')
                ->label('Todos')
                ->color('gray')
                ->badge(\App\Models\User::count())
                ->url(route('filament.admin.resources.users.index')),
            
            Actions\Action::make('filter_pf')
                ->label('Pessoa Física')
                ->color('success')
                ->badge(\App\Models\User::where('user_type', 'pf')->count())
                ->url(route('filament.admin.resources.users.index', ['tableFilters' => ['user_type' => ['value' => 'pf']]])),
            
            Actions\Action::make('filter_pj')
                ->label('Pessoa Jurídica')
                ->color('info')
                ->badge(\App\Models\User::where('user_type', 'pj')->count())
                ->url(route('filament.admin.resources.users.index', ['tableFilters' => ['user_type' => ['value' => 'pj']]])),
            
            Actions\CreateAction::make(),
        ];
    }
}
