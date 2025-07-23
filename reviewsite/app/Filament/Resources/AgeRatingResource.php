<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgeRatingResource\Pages;
use App\Models\AgeRating;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AgeRatingResource extends Resource
{
    protected static ?string $model = AgeRating::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Age Ratings';
    protected static ?string $navigationGroup = 'Categories';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')->required()->maxLength(255),
            Forms\Components\Select::make('type')->options([
                'esrb' => 'ESRB',
                'pegi' => 'PEGI',
            ])->required(),
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

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListAgeRatings::route('/'),
            'create' => Pages\CreateAgeRating::route('/create'),
            'edit' => Pages\EditAgeRating::route('/{record}/edit'),
        ];
    }
} 