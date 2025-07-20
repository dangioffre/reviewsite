<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ListModelResource\Pages;
use App\Filament\Resources\ListModelResource\RelationManagers;
use App\Models\ListModel;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;

class ListModelResource extends Resource
{
    protected static ?string $model = ListModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationLabel = 'User Lists';
    
    protected static ?string $modelLabel = 'User List';
    
    protected static ?string $pluralModelLabel = 'User Lists';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('List Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $context, $state, Forms\Set $set) => 
                                $context === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null
                            ),
                            
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ListModel::class, 'slug', ignoreRecord: true)
                            ->helperText('URL-friendly version of the list name'),
                            
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->maxLength(1000)
                            ->rows(3),
                            
                        Forms\Components\Select::make('user_id')
                            ->label('Owner')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                            
                        Forms\Components\Select::make('category')
                            ->options(ListModel::$categories)
                            ->required()
                            ->default('general'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Privacy & Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_public')
                            ->label('Public List')
                            ->helperText('Public lists can be viewed by anyone and appear on the public lists page')
                            ->default(false),
                            
                        Forms\Components\Toggle::make('allow_collaboration')
                            ->label('Allow Collaboration')
                            ->helperText('Allow other users to add items to this list')
                            ->default(false),
                            
                        Forms\Components\Toggle::make('allow_comments')
                            ->label('Allow Comments')
                            ->helperText('Allow users to comment on this list')
                            ->default(true),
                    ])->columns(3),
                    
                Forms\Components\Section::make('Sorting Options')
                    ->schema([
                        Forms\Components\Select::make('sort_by')
                            ->label('Sort Items By')
                            ->options(ListModel::$sortOptions ?? [
                                'date_added' => 'Date Added',
                                'name' => 'Name',
                                'rating' => 'Rating',
                            ])
                            ->default('date_added'),
                            
                        Forms\Components\Select::make('sort_direction')
                            ->label('Sort Direction')
                            ->options([
                                'asc' => 'Ascending',
                                'desc' => 'Descending',
                            ])
                            ->default('desc'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->description(fn (ListModel $record): string => $record->description ? \Illuminate\Support\Str::limit($record->description, 50) : ''),
                    
                Tables\Columns\BadgeColumn::make('is_public')
                    ->label('Visibility')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Public' : 'Private')
                    ->colors([
                        'success' => fn ($state): bool => $state === true,
                        'warning' => fn ($state): bool => $state === false,
                    ])
                    ->icons([
                        'heroicon-m-globe-alt' => fn ($state): bool => $state === true,
                        'heroicon-m-lock-closed' => fn ($state): bool => $state === false,
                    ])
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('category')
                    ->formatStateUsing(fn (string $state): string => ListModel::$categories[$state] ?? ucfirst($state))
                    ->colors([
                        'primary' => 'general',
                        'success' => 'completed',
                        'warning' => 'wishlist',
                        'info' => 'playing',
                        'danger' => 'favorites',
                    ]),
                    
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->badge()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('followers_count')
                    ->label('Followers')
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('comments_count')
                    ->label('Comments')
                    ->numeric()
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('allow_collaboration')
                    ->label('Collab')
                    ->boolean()
                    ->tooltip('Collaboration Allowed'),
                    
                Tables\Columns\IconColumn::make('allow_comments')
                    ->label('Comments')
                    ->boolean()
                    ->tooltip('Comments Allowed'),
                    
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
                Tables\Filters\SelectFilter::make('is_public')
                    ->label('Visibility')
                    ->options([
                        true => 'Public',
                        false => 'Private',
                    ])
                    ->placeholder('All Lists'),
                    
                Tables\Filters\SelectFilter::make('category')
                    ->options(ListModel::$categories),
                    
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Owner')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\Filter::make('has_items')
                    ->label('With Items')
                    ->query(fn (Builder $query): Builder => $query->has('items')),
                    
                Tables\Filters\Filter::make('popular')
                    ->label('Popular (5+ followers)')
                    ->query(fn (Builder $query): Builder => $query->where('followers_count', '>=', 5)),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle_visibility')
                    ->label(fn (ListModel $record): string => $record->is_public ? 'Make Private' : 'Make Public')
                    ->icon(fn (ListModel $record): string => $record->is_public ? 'heroicon-m-lock-closed' : 'heroicon-m-globe-alt')
                    ->color(fn (ListModel $record): string => $record->is_public ? 'warning' : 'success')
                    ->action(fn (ListModel $record) => $record->update(['is_public' => !$record->is_public]))
                    ->requiresConfirmation()
                    ->modalDescription('Are you sure you want to change the visibility of this list?'),
                    
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->url(fn (ListModel $record): string => $record->is_public ? route('lists.public', $record->slug) : '#')
                    ->openUrlInNewTab()
                    ->visible(fn (ListModel $record): bool => $record->is_public),
                    
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('make_public')
                        ->label('Make Public')
                        ->icon('heroicon-m-globe-alt')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_public' => true]))
                        ->requiresConfirmation(),
                        
                    Tables\Actions\BulkAction::make('make_private')
                        ->label('Make Private')
                        ->icon('heroicon-m-lock-closed')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['is_public' => false]))
                        ->requiresConfirmation(),
                        
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListListModels::route('/'),
            'create' => Pages\CreateListModel::route('/create'),
            'edit' => Pages\EditListModel::route('/{record}/edit'),
        ];
    }
}
