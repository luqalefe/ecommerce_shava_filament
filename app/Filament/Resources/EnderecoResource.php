<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnderecoResource\Pages;
use App\Models\Endereco;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EnderecoResource extends Resource
{
    protected static ?string $model = Endereco::class;

    // Traduções e Agrupamento
    protected static ?string $modelLabel = 'Endereço';
    protected static ?string $pluralModelLabel = 'Endereços';
    protected static ?string $navigationGroup = 'Loja';
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('usuario_id')
                    ->relationship('usuario', 'name')
                    ->label('Cliente')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('rua')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('numero')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('complemento')
                    ->maxLength(255),
                Forms\Components\TextInput::make('cidade')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('estado')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('cep')
                    ->label('CEP')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('usuario.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rua')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cidade')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cep')
                    ->label('CEP')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnderecos::route('/'),
            'create' => Pages\CreateEndereco::route('/create'),
            'edit' => Pages\EditEndereco::route('/{record}/edit'),
        ];
    }
}