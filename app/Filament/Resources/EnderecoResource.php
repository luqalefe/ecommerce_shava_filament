<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnderecoResource\Pages;
use App\Models\Endereco;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class EnderecoResource extends Resource
{
    protected static ?string $model = Endereco::class;

    protected static ?string $modelLabel = 'Endereço';
    protected static ?string $pluralModelLabel = 'Endereços';
    protected static ?string $navigationGroup = 'Loja';
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id') // <-- A correção final
                    ->relationship(name: 'user', titleAttribute: 'name')
                    ->label('Cliente')
                    ->searchable()
                    ->preload() // Boa prática para campos de busca
                    ->required(),
                Forms\Components\TextInput::make('rua')->required()->maxLength(255),
                Forms\Components\TextInput::make('numero')->required()->maxLength(255),
                Forms\Components\TextInput::make('complemento')->maxLength(255),
                Forms\Components\TextInput::make('cidade')->required()->maxLength(255),
                Forms\Components\TextInput::make('estado')->required()->maxLength(255),
                Forms\Components\TextInput::make('cep')->label('CEP')->required()->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rua')->searchable(),
                Tables\Columns\TextColumn::make('cidade')->searchable(),
                Tables\Columns\TextColumn::make('cep')->label('CEP')->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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

    /**
     * Verifica se o recurso deve aparecer no menu de navegação
     * Apenas administradores podem gerenciar endereços
     */
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user() && Auth::user()->isAdmin();
    }
}