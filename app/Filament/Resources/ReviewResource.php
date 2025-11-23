<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    
    protected static ?string $modelLabel = 'Avaliação';
    protected static ?string $pluralModelLabel = 'Avaliações';
    protected static ?string $navigationGroup = 'Gestão da Loja';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Produto')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('user_id')
                    ->label('Usuário')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('rating')
                    ->label('Avaliação')
                    ->options([
                        1 => '1 Estrela',
                        2 => '2 Estrelas',
                        3 => '3 Estrelas',
                        4 => '4 Estrelas',
                        5 => '5 Estrelas',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('comment')
                    ->label('Comentário')
                    ->rows(4)
                    ->maxLength(1000),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produto')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuário')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Avaliação')
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        1, 2 => 'danger',
                        3 => 'warning',
                        4, 5 => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state)),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Comentário')
                    ->limit(50)
                    ->tooltip(fn (Review $record): string => $record->comment ?? ''),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->label('Avaliação')
                    ->options([
                        1 => '1 Estrela',
                        2 => '2 Estrelas',
                        3 => '3 Estrelas',
                        4 => '4 Estrelas',
                        5 => '5 Estrelas',
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }

    /**
     * Verifica se o recurso deve aparecer no menu de navegação
     * Apenas administradores podem gerenciar avaliações
     */
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user() && Auth::user()->isAdmin();
    }
}