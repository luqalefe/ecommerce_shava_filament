<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $modelLabel = 'Produto';
    protected static ?string $pluralModelLabel = 'Produtos';
    protected static ?string $navigationGroup = 'Loja';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informações Principais')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome do Produto')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->unique(Product::class, 'slug', ignoreRecord: true),
                            ])->columns(2),

                        Forms\Components\Section::make('Descrições')
                            ->schema([
                                Forms\Components\RichEditor::make('long_description')
                                    ->label('Descrição Completa'),
                                Forms\Components\Textarea::make('short_description')
                                    ->label('Descrição Curta (resumo)'),
                            ]),

                        // ===== CORREÇÃO ESTÁ AQUI =====
                        Forms\Components\Section::make('Imagens')
                            ->schema([
                                Forms\Components\Repeater::make('images') // Alterado de 'productImages' para 'images'
                                    ->label('Imagens do Produto')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\FileUpload::make('path')
                                            ->label('Imagem')
                                            ->disk('public')
                                            ->directory('product-images')
                                            ->required(),
                                        Forms\Components\Toggle::make('is_main')->label('Principal?')->default(false),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Preço e Estoque')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('Preço')
                                    ->numeric()
                                    ->prefix('R$')
                                    ->required(),

                                Forms\Components\TextInput::make('sale_price')
                                    ->label('Preço Promocional')
                                    ->numeric()
                                    ->prefix('R$'),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('Quantidade em Estoque')
                                    ->numeric()
                                    ->default(0),

                                Forms\Components\TextInput::make('sku')
                                    ->label('SKU (Código)'),
                            ]),

                        Forms\Components\Section::make('Associações')
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->label('Categoria')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->required(),

                                Forms\Components\Select::make('attributeValues')
                                    ->label('Atributos (Cores, Tamanhos)')
                                    ->relationship('attributeValues', 'value')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),
                            ]),

                        Forms\Components\Section::make('Dimensões e Peso')
                            ->description('Informações necessárias para cálculo de frete')
                            ->schema([
                                Forms\Components\TextInput::make('weight')
                                    ->label('Peso (kg)')
                                    ->required()
                                    ->suffix('kg')
                                    ->default('0.5')
                                    ->placeholder('Ex: 0.5')
                                    ->inputMode('decimal')
                                    ->rule('numeric')
                                    ->rule('min:0.01')
                                    ->helperText('Peso do produto em quilogramas'),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('height')
                                            ->label('Altura (cm)')
                                            ->required()
                                            ->suffix('cm')
                                            ->default('10')
                                            ->placeholder('10')
                                            ->inputMode('decimal')
                                            ->rule('numeric')
                                            ->rule('min:1'),

                                        Forms\Components\TextInput::make('width')
                                            ->label('Largura (cm)')
                                            ->required()
                                            ->suffix('cm')
                                            ->default('10')
                                            ->placeholder('15')
                                            ->inputMode('decimal')
                                            ->rule('numeric')
                                            ->rule('min:1'),

                                        Forms\Components\TextInput::make('length')
                                            ->label('Comprimento (cm)')
                                            ->required()
                                            ->suffix('cm')
                                            ->default('10')
                                            ->placeholder('20')
                                            ->inputMode('decimal')
                                            ->rule('numeric')
                                            ->rule('min:1'),
                                    ]),
                            ])
                            ->collapsible(),

                        Forms\Components\Section::make('Status')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Produto Ativo?')
                                    ->helperText('Produtos inativos não serão exibidos na loja.')
                                    ->default(true),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Preço')
                    ->money('BRL')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    /**
     * Verifica se o recurso deve aparecer no menu de navegação
     * Apenas administradores podem gerenciar produtos
     */
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user() && Auth::user()->isAdmin();
    }
}