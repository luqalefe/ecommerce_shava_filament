<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $modelLabel = 'Usuário';
    protected static ?string $pluralModelLabel = 'Usuários';
    protected static ?string $navigationGroup = 'Gestão de Clientes';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Básicas')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome / Razão Social')
                            ->helperText('Para PJ: digite a Razão Social aqui')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->label('Senha')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('celular')
                            ->label('Celular')
                            ->mask('(99) 99999-9999')
                            ->maxLength(20),
                    ])->columns(2),

                Forms\Components\Section::make('Tipo de Pessoa')
                    ->schema([
                        Forms\Components\Select::make('user_type')
                            ->label('Tipo de Pessoa')
                            ->options([
                                'pf' => 'Pessoa Física',
                                'pj' => 'Pessoa Jurídica',
                            ])
                            ->default('pf')
                            ->required()
                            ->live(), // Atualiza campos dinamicamente

                        // Campos Pessoa Física
                        Forms\Components\TextInput::make('cpf')
                            ->label('CPF')
                            ->mask('999.999.999-99')
                            ->maxLength(14)
                            ->required(fn (Forms\Get $get) => $get('user_type') === 'pf')
                            ->visible(fn (Forms\Get $get) => $get('user_type') === 'pf'),

                        // Campos Pessoa Jurídica
                        Forms\Components\TextInput::make('cnpj')
                            ->label('CNPJ')
                            ->mask('99.999.999/9999-99')
                            ->maxLength(18)
                            ->required(fn (Forms\Get $get) => $get('user_type') === 'pj')
                            ->visible(fn (Forms\Get $get) => $get('user_type') === 'pj'),

                        Forms\Components\TextInput::make('razao_social')
                            ->label('Razão Social')
                            ->helperText('Nome legal da empresa (já deve estar preenchido acima)')
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get) => $get('user_type') === 'pj'),

                        Forms\Components\TextInput::make('nome_fantasia')
                            ->label('Nome Fantasia')
                            ->helperText('Nome comercial da empresa (opcional)')
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get) => $get('user_type') === 'pj'),

                        Forms\Components\TextInput::make('inscricao_estadual')
                            ->label('Inscrição Estadual')
                            ->maxLength(20)
                            ->visible(fn (Forms\Get $get) => $get('user_type') === 'pj'),
                    ])->columns(2),

                Forms\Components\Section::make('Endereço Comercial')
                    ->description('Obrigatório para Pessoa Jurídica - será usado para mapeamento de clientes')
                    ->schema([
                        Forms\Components\TextInput::make('endereco_cep')
                            ->label('CEP')
                            ->mask('99999-999')
                            ->maxLength(9)
                            ->required(fn (Forms\Get $get) => $get('user_type') === 'pj')
                            ->helperText('CEP do estabelecimento comercial'),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('endereco_rua')
                                    ->label('Rua/Avenida')
                                    ->maxLength(255)
                                    ->required(fn (Forms\Get $get) => $get('user_type') === 'pj')
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('endereco_numero')
                                    ->label('Número')
                                    ->maxLength(20)
                                    ->required(fn (Forms\Get $get) => $get('user_type') === 'pj'),
                            ]),

                        Forms\Components\TextInput::make('endereco_complemento')
                            ->label('Complemento')
                            ->maxLength(255)
                            ->helperText('Sala, andar, bloco, etc (opcional)'),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('endereco_cidade')
                                    ->label('Cidade')
                                    ->maxLength(255)
                                    ->required(fn (Forms\Get $get) => $get('user_type') === 'pj'),

                                Forms\Components\Select::make('endereco_estado')
                                    ->label('Estado')
                                    ->options([
                                        'AC' => 'Acre',
                                        'AL' => 'Alagoas',
                                        'AP' => 'Amapá',
                                        'AM' => 'Amazonas',
                                        'BA' => 'Bahia',
                                        'CE' => 'Ceará',
                                        'DF' => 'Distrito Federal',
                                        'ES' => 'Espírito Santo',
                                        'GO' => 'Goiás',
                                        'MA' => 'Maranhão',
                                        'MT' => 'Mato Grosso',
                                        'MS' => 'Mato Grosso do Sul',
                                        'MG' => 'Minas Gerais',
                                        'PA' => 'Pará',
                                        'PB' => 'Paraíba',
                                        'PR' => 'Paraná',
                                        'PE' => 'Pernambuco',
                                        'PI' => 'Piauí',
                                        'RJ' => 'Rio de Janeiro',
                                        'RN' => 'Rio Grande do Norte',
                                        'RS' => 'Rio Grande do Sul',
                                        'RO' => 'Rondônia',
                                        'RR' => 'Roraima',
                                        'SC' => 'Santa Catarina',
                                        'SP' => 'São Paulo',
                                        'SE' => 'Sergipe',
                                        'TO' => 'Tocantins',
                                    ])
                                    ->searchable()
                                    ->required(fn (Forms\Get $get) => $get('user_type') === 'pj'),
                            ]),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('user_type') === 'pj')
                    ->collapsible(),

                Forms\Components\Section::make('Permissões')
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->label('Perfil')
                            ->options([
                                'user' => 'Usuário',
                                'logistica' => 'Logística',
                                'admin' => 'Administrador',
                            ])
                            ->default('user')
                            ->required()
                            ->visible(fn () => Auth::user() && Auth::user()->isAdmin()),
                        Forms\Components\Toggle::make('is_admin')
                            ->label('É Administrador')
                            ->default(false)
                            ->visible(fn () => Auth::user() && Auth::user()->isAdmin()),
                    ])->columns(2)
                    ->visible(fn () => Auth::user() && Auth::user()->isAdmin()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user_type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pf' => 'success',
                        'pj' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pf' => 'Pessoa Física',
                        'pj' => 'Pessoa Jurídica',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('cpf')
                    ->label('CPF')
                    ->searchable(),
                Tables\Columns\TextColumn::make('celular')
                    ->label('Celular')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Perfil')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'logistica' => 'info',
                        'user' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Administrador',
                        'logistica' => 'Logística',
                        'user' => 'Usuário',
                        default => $state,
                    })
                    ->visible(fn () => Auth::user() && Auth::user()->isAdmin()),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_type')
                    ->label('Tipo de Pessoa')
                    ->options([
                        'pf' => 'Pessoa Física',
                        'pj' => 'Pessoa Jurídica',
                    ]),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    /**
     * Verifica se o recurso deve aparecer no menu de navegação
     * Apenas administradores podem gerenciar usuários
     */
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user() && Auth::user()->isAdmin();
    }
}