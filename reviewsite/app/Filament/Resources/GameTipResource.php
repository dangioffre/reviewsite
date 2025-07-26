<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameTipResource\Pages;
use App\Models\GameTip;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class GameTipResource extends Resource
{
    protected static ?string $model = GameTip::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationGroup = 'Game Content';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Tip Information')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Game')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('user_id')
                            ->label('Submitted By')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('game_tip_category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('content')
                            ->label('Content (Markdown)')
                            ->required()
                            ->rows(10)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('youtube_link')
                            ->label('YouTube Video Link')
                            ->url()
                            ->placeholder('https://www.youtube.com/watch?v=...'),

                        Forms\Components\CheckboxList::make('tags')
                            ->label('Tags')
                            ->options([
                                'Spoiler' => '[Spoiler]',
                                'Patch Dependent' => '[Patch Dependent]',
                                'Outdated' => '[Outdated]',
                                'Beginner' => '[Beginner]',
                                'Advanced' => '[Advanced]',
                                'Exploit' => '[Exploit]',
                            ])
                            ->columns(3),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Game')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Submitted By')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),

                Tables\Columns\TextColumn::make('likes_count')
                    ->label('Likes')
                    ->sortable(),

                Tables\Columns\TextColumn::make('comments_count')
                    ->label('Comments')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),

                Tables\Filters\SelectFilter::make('game_tip_category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),

                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Game')
                    ->relationship('product', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn (GameTip $record) => "Tip: {$record->title}")
                    ->modalContent(fn (GameTip $record) => view('filament.resources.game-tip-resource.view-modal', [
                        'tip' => $record->load(['user', 'product', 'category']),
                    ]))
                    ->modalWidth('4xl')
                    ->modalActions([
                        Tables\Actions\Action::make('approve')
                            ->label('Approve')
                            ->color('success')
                            ->icon('heroicon-o-check')
                            ->visible(fn (GameTip $record) => $record->status === 'pending')
                            ->action(fn (GameTip $record) => $record->update(['status' => 'approved']))
                            ->requiresConfirmation()
                            ->modalHeading('Approve Tip')
                            ->modalDescription('Are you sure you want to approve this tip? It will be visible to all users.')
                            ->modalSubmitActionLabel('Yes, approve'),

                        Tables\Actions\Action::make('reject')
                            ->label('Reject')
                            ->color('danger')
                            ->icon('heroicon-o-x-mark')
                            ->visible(fn (GameTip $record) => $record->status === 'pending')
                            ->action(fn (GameTip $record) => $record->update(['status' => 'rejected']))
                            ->requiresConfirmation()
                            ->modalHeading('Reject Tip')
                            ->modalDescription('Are you sure you want to reject this tip? This action cannot be undone.')
                            ->modalSubmitActionLabel('Yes, reject'),

                        Tables\Actions\Action::make('close')
                            ->label('Close')
                            ->color('gray')
                            ->modalSubmitAction(false)
                            ->closeModalByClickingAway(false),
                    ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve')
                        ->action(fn (Collection $records) => $records->each(fn ($record) => $record->update(['status' => 'approved'])))
                        ->requiresConfirmation()
                        ->color('success')
                        ->icon('heroicon-o-check'),

                    Tables\Actions\BulkAction::make('reject')
                        ->action(fn (Collection $records) => $records->each(fn ($record) => $record->update(['status' => 'rejected'])))
                        ->requiresConfirmation()
                        ->color('danger')
                        ->icon('heroicon-o-x-mark'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListGameTips::route('/'),
            'create' => Pages\CreateGameTip::route('/create'),
            'edit' => Pages\EditGameTip::route('/{record}/edit'),
        ];
    }
}
