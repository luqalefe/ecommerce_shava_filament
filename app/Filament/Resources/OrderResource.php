<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $modelLabel = 'Pedido';
    protected static ?string $pluralModelLabel = 'Pedidos';
    protected static ?string $navigationGroup = 'Gestão da Loja';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Cliente')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('endereco_id')
                    ->label('Endereço')
                    ->relationship('endereco', 'rua')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pendente',
                        'processing' => 'Processando',
                        'shipped' => 'Enviado',
                        'delivered' => 'Entregue',
                        'cancelled' => 'Cancelado',
                    ])
                    ->required()
                    ->searchable()
                    ->live(),
                Forms\Components\TextInput::make('total_amount')
                    ->label('Valor Total')
                    ->numeric()
                    ->prefix('R$')
                    ->required(),
                Forms\Components\TextInput::make('shipping_cost')
                    ->label('Custo do Frete')
                    ->numeric()
                    ->prefix('R$'),
                Forms\Components\TextInput::make('shipping_service')
                    ->label('Serviço de Entrega')
                    ->maxLength(255),
                Forms\Components\Select::make('payment_method')
                    ->label('Método de Pagamento')
                    ->options([
                        'pix' => 'PIX',
                        'card' => 'Cartão de Crédito',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('payment_id')
                    ->label('ID do Pagamento')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendente',
                        'processing' => 'Processando',
                        'shipped' => 'Enviado',
                        'delivered' => 'Entregue',
                        'cancelled' => 'Cancelado',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Valor Total')
                    ->money('BRL')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Pagamento')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pendente',
                        'processing' => 'Processando',
                        'shipped' => 'Enviado',
                        'delivered' => 'Entregue',
                        'cancelled' => 'Cancelado',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('marcar_processando')
                        ->label('Marcar como Processando')
                        ->icon('heroicon-o-cog')
                        ->color('info')
                        ->action(function (Order $record) {
                            \Illuminate\Support\Facades\Log::info('Ação marcar_processando INICIADA', [
                                'order_id' => $record->id,
                                'current_status' => $record->status,
                            ]);
                            
                            try {
                                $record->update(['status' => 'processing']);
                                
                                \Illuminate\Support\Facades\Log::info('Ação marcar_processando CONCLUÍDA', [
                                    'order_id' => $record->id,
                                    'new_status' => $record->fresh()->status,
                                ]);
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Status atualizado para Processando')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error('Erro ao atualizar status', [
                                    'order_id' => $record->id,
                                    'error' => $e->getMessage(),
                                ]);
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Erro ao atualizar status')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->visible(fn (Order $record) => $record->status !== 'processing'),
                    Tables\Actions\Action::make('marcar_enviado')
                        ->label('Marcar como Enviado')
                        ->icon('heroicon-o-truck')
                        ->color('primary')
                        ->action(function (Order $record) {
                            \Illuminate\Support\Facades\Log::info('Ação marcar_enviado INICIADA', [
                                'order_id' => $record->id,
                                'current_status' => $record->status,
                            ]);
                            
                            try {
                                $record->update(['status' => 'shipped']);
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Status atualizado para Enviado')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Erro ao atualizar status')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->visible(fn (Order $record) => $record->status !== 'shipped'),
                    Tables\Actions\Action::make('marcar_entregue')
                        ->label('Marcar como Entregue')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Order $record) {
                            \Illuminate\Support\Facades\Log::info('Ação marcar_entregue INICIADA', [
                                'order_id' => $record->id,
                                'current_status' => $record->status,
                            ]);
                            
                            try {
                                $record->update(['status' => 'delivered']);
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Status atualizado para Entregue')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Erro ao atualizar status')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->visible(fn (Order $record) => $record->status !== 'delivered'),
                    Tables\Actions\Action::make('cancelar')
                        ->label('Cancelar Pedido')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation() // Mantém confirmação apenas para cancelar
                        ->action(function (Order $record) {
                            \Illuminate\Support\Facades\Log::info('Ação cancelar INICIADA', [
                                'order_id' => $record->id,
                                'current_status' => $record->status,
                            ]);
                            
                            try {
                                $record->update(['status' => 'cancelled']);
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Pedido cancelado')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Erro ao cancelar pedido')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->visible(fn (Order $record) => $record->status !== 'cancelled'),
                ])
                ->label('Alterar Status')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'), // RESTAURADO
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}