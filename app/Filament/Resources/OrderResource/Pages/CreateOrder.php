<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function authorizeAccess(): void
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403, 'Apenas administradores podem criar pedidos.');
        }
    }
}
