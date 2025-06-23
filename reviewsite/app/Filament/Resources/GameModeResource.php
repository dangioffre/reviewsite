<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameModeResource\Pages;
use App\Filament\Resources\GameModeResource\RelationManagers;
use App\Models\GameMode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GameModeResource extends Resource
{
    protected static ?string $model = GameMode::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';
    
    protected static ?string $navigationGroup = 'Categories';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Game Mode Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $context, $state, Forms\Set $set) {
                                if ($context === 'create') {
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(GameMode::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash']),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\ColorPicker::make('color')
                            ->default('#6B7280')
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Slug copied')
                    ->copyMessageDuration(1500),

                Tables\Columns\ColorColumn::make('color')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Products')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueLabel('Only active game modes')
                    ->falseLabel('Only inactive game modes')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
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
            'index' => Pages\ListGameModes::route('/'),
            'create' => Pages\CreateGameMode::route('/create'),
            'edit' => Pages\EditGameMode::route('/{record}/edit'),
        ];
    }
}
