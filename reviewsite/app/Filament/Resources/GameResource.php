<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameResource\Pages;
use App\Filament\Resources\GameResource\RelationManagers;
use App\Models\Product;
use App\Models\Genre;
use App\Models\Platform;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GameResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';
    protected static ?string $navigationLabel = 'Games';
    protected static ?string $navigationGroup = 'Review Management';
    protected static ?string $slug = 'games';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Game Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('genre_id')
                            ->label('Genre')
                            ->options(Genre::active()->pluck('name', 'id'))
                            ->searchable(),
                        
                        Forms\Components\Select::make('platform_id')
                            ->label('Platform')
                            ->options(Platform::active()->pluck('name', 'id'))
                            ->searchable(),
                        
                        Forms\Components\TextInput::make('image')
                            ->label('Image URL')
                            ->url(),
                        
                        Forms\Components\TextInput::make('video')
                            ->label('Video Embed URL')
                            ->url(),
                            
                        Forms\Components\TextInput::make('staff_rating')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10)
                            ->step(0.1),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Staff Review')
                    ->schema([
                        Forms\Components\RichEditor::make('staff_review')
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Hidden::make('type')
                    ->default('game'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->size(60),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('genre.name')
                    ->badge()
                    ->color(fn ($record) => $record->genre?->color ?: 'gray'),
                
                Tables\Columns\TextColumn::make('platform.name')
                    ->badge()
                    ->color(fn ($record) => $record->platform?->color ?: 'gray'),
                
                Tables\Columns\TextColumn::make('staff_rating')
                    ->sortable()
                    ->badge()
                    ->color(function ($state) {
                        if ($state >= 9) return 'success';
                        if ($state >= 7) return 'warning';
                        if ($state >= 5) return 'info';
                        return 'danger';
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('genre')
                    ->relationship('genre', 'name'),
                
                Tables\Filters\SelectFilter::make('platform')
                    ->relationship('platform', 'name'),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'game');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGames::route('/'),
            'create' => Pages\CreateGame::route('/create'),
            'edit' => Pages\EditGame::route('/{record}/edit'),
        ];
    }
}
