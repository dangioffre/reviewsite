<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameResource\Pages;
use App\Filament\Resources\GameResource\RelationManagers;
use App\Models\Product;
use App\Models\Genre;
use App\Models\Platform;
use App\Models\Hardware;
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
    protected static ?string $navigationLabel = 'Products';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'games';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('type')
                            ->required()
                            ->options([
                                'game' => 'Game',
                                'hardware' => 'Hardware',
                                'accessory' => 'Accessory',
                            ])
                            ->live(),
                        
                        Forms\Components\Select::make('genre_id')
                            ->label('Genre')
                            ->options(Genre::active()->pluck('name', 'id'))
                            ->searchable(),
                        
                        Forms\Components\Select::make('platform_id')
                            ->label('Platform')
                            ->options(Platform::active()->pluck('name', 'id'))
                            ->searchable(),
                        
                        Forms\Components\Select::make('hardware_id')
                            ->label('Hardware (for Accessories)')
                            ->options(Hardware::active()->pluck('name', 'id'))
                            ->searchable()
                            ->visible(fn (Forms\Get $get) => $get('type') === 'accessory')
                            ->helperText('Select the hardware this accessory is compatible with'),
                        
                        Forms\Components\TextInput::make('image')
                            ->label('Image URL')
                            ->url(),
                        
                        Forms\Components\TextInput::make('video_url')
                            ->label('Video Embed URL')
                            ->url(),
                        
                        Forms\Components\DatePicker::make('release_date')
                            ->label('Release Date'),
                        
                        Forms\Components\TextInput::make('developer')
                            ->label('Developer/Manufacturer')
                            ->maxLength(255),
                            
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
                
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(function ($state) {
                        return match($state) {
                            'game' => 'success',
                            'hardware' => 'warning',
                            'accessory' => 'info',
                            default => 'gray',
                        };
                    }),
                
                Tables\Columns\TextColumn::make('genre.name')
                    ->badge()
                    ->color(fn ($record) => $record->genre?->color ?: 'gray'),
                
                Tables\Columns\TextColumn::make('platform.name')
                    ->badge()
                    ->color(fn ($record) => $record->platform?->color ?: 'gray'),
                
                Tables\Columns\TextColumn::make('hardware.name')
                    ->badge()
                    ->color(fn ($record) => $record->hardware?->color ?: 'gray')
                    ->visible(fn ($record) => $record->type === 'accessory'),
                
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
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'game' => 'Game',
                        'hardware' => 'Hardware', 
                        'accessory' => 'Accessory',
                    ]),
                
                Tables\Filters\SelectFilter::make('hardware')
                    ->relationship('hardware', 'name'),
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
        return parent::getEloquentQuery();
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
