<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PodcastResource\Pages;
use App\Models\Podcast;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Notifications\Notification;

class PodcastResource extends Resource
{
    protected static ?string $model = Podcast::class;

    protected static ?string $navigationIcon = 'heroicon-o-microphone';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Podcast Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $context, $state, callable $set) {
                                if ($context === 'create') {
                                    $set('slug', \Illuminate\Support\Str::slug($state));
                                }
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->rules(['alpha_dash']),

                        Forms\Components\Select::make('owner_id')
                            ->label('Owner')
                            ->relationship('owner', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('rss_url')
                            ->label('RSS URL')
                            ->url()
                            ->required()
                            ->maxLength(500),

                        Forms\Components\TextInput::make('website_url')
                            ->label('Website URL')
                            ->url()
                            ->maxLength(500),

                        Forms\Components\TextInput::make('logo_url')
                            ->label('Logo URL')
                            ->url()
                            ->maxLength(500),

                        Forms\Components\TagsInput::make('hosts')
                            ->label('Hosts')
                            ->placeholder('Add host names...')
                            ->columnSpanFull(),

                        Forms\Components\KeyValue::make('links')
                            ->label('Social Links')
                            ->keyLabel('Platform')
                            ->valueLabel('URL')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Status & Verification')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'verified' => 'Verified',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\Toggle::make('verification_status')
                            ->label('Verified')
                            ->disabled(),

                        Forms\Components\TextInput::make('verification_token')
                            ->label('Verification Token')
                            ->disabled()
                            ->visible(fn ($get) => $get('status') === 'pending'),

                        Forms\Components\Select::make('approved_by')
                            ->label('Approved By')
                            ->relationship('approvedBy', 'name')
                            ->disabled()
                            ->visible(fn ($get) => in_array($get('status'), ['approved', 'rejected'])),

                        Forms\Components\DateTimePicker::make('approved_at')
                            ->label('Approved At')
                            ->disabled()
                            ->visible(fn ($get) => in_array($get('status'), ['approved', 'rejected'])),

                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('RSS Information')
                    ->schema([
                        Forms\Components\DateTimePicker::make('last_rss_check')
                            ->label('Last RSS Check')
                            ->disabled(),

                        Forms\Components\Textarea::make('rss_error')
                            ->label('RSS Error')
                            ->rows(2)
                            ->disabled()
                            ->visible(fn ($get) => !empty($get('rss_error'))),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_url')
                    ->label('Logo')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(url('/images/default-podcast.png')),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'verified',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'verified',
                        'heroicon-o-shield-check' => 'approved',
                        'heroicon-o-x-circle' => 'rejected',
                    ]),

                Tables\Columns\IconColumn::make('verification_status')
                    ->label('Verified')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('episodes_count')
                    ->label('Episodes')
                    ->counts('episodes')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reviews_count')
                    ->label('Reviews')
                    ->counts('reviews')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Approved')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),

                Tables\Filters\TernaryFilter::make('verification_status')
                    ->label('Verified')
                    ->placeholder('All podcasts')
                    ->trueLabel('Verified')
                    ->falseLabel('Not verified'),

                Tables\Filters\Filter::make('has_rss_error')
                    ->label('Has RSS Error')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('rss_error')),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Submitted from'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Submitted until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Podcast $record): bool => in_array($record->status, ['verified', 'rejected']))
                    ->requiresConfirmation()
                    ->modalHeading('Approve Podcast')
                    ->modalDescription('Are you sure you want to approve this podcast? It will be publicly visible.')
                    ->action(function (Podcast $record): void {
                        $wasPending = in_array($record->status, ['pending', 'verified']);

                        $record->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                        ]);

                        // If it was just approved for the first time, run an initial sync
                        if ($wasPending) {
                            $service = app(\App\Services\RssVerificationService::class);
                            $count = $service->importEpisodes($record);
                            
                            Notification::make()
                                ->title("Initial Sync: Imported {$count} episodes")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Podcast approved successfully')
                                ->success()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Podcast $record): bool => in_array($record->status, ['pending', 'verified']))
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Rejection reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Podcast $record, array $data): void {
                        $record->update([
                            'status' => 'rejected',
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                            'admin_notes' => $data['admin_notes'],
                        ]);

                        Notification::make()
                            ->title('Podcast rejected')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('syncRss')
                    ->label('Sync RSS')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->visible(fn (Podcast $record): bool => $record->status === 'approved')
                    ->action(function (Podcast $record): void {
                        $service = app(\App\Services\RssVerificationService::class);
                        $count = $service->importEpisodes($record);
                        
                        if ($count > 0) {
                            Notification::make()
                                ->title("Imported {$count} new episodes")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('No new episodes found')
                                ->warning()
                                ->send();
                        }
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            $records->each(function (Podcast $record) {
                                if (in_array($record->status, ['verified', 'rejected'])) {
                                    $record->update([
                                        'status' => 'approved',
                                        'approved_at' => now(),
                                        'approved_by' => auth()->id(),
                                    ]);
                                }
                            });

                            Notification::make()
                                ->title('Selected podcasts approved')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Podcast Information')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->weight(FontWeight::Bold)
                                    ->size('lg'),

                                Infolists\Components\TextEntry::make('slug')
                                    ->badge()
                                    ->color('gray'),

                                Infolists\Components\TextEntry::make('owner.name')
                                    ->label('Owner'),

                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'verified' => 'primary',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                    }),

                                Infolists\Components\TextEntry::make('description')
                                    ->columnSpanFull(),

                                Infolists\Components\TextEntry::make('rss_url')
                                    ->label('RSS URL')
                                    ->url(fn (Podcast $record): ?string => $record->rss_url)
                                    ->openUrlInNewTab(),

                                Infolists\Components\TextEntry::make('website_url')
                                    ->label('Website')
                                    ->url(fn (Podcast $record): ?string => $record->website_url)
                                    ->visible(fn (Podcast $record): bool => !empty($record->website_url)),

                                Infolists\Components\TextEntry::make('hosts')
                                    ->badge()
                                    ->separator(',')
                                    ->visible(fn ($record) => !empty($record->hosts)),

                                Infolists\Components\KeyValueEntry::make('links')
                                    ->label('Social Links')
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => !empty($record->links)),
                            ]),
                    ]),

                Infolists\Components\Section::make('Status & Verification')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\IconEntry::make('verification_status')
                                    ->label('Verified')
                                    ->boolean(),

                                Infolists\Components\TextEntry::make('verification_token')
                                    ->label('Verification Token')
                                    ->visible(fn ($record) => $record->status === 'pending'),

                                Infolists\Components\TextEntry::make('approvedBy.name')
                                    ->label('Approved By')
                                    ->visible(fn ($record) => in_array($record->status, ['approved', 'rejected'])),

                                Infolists\Components\TextEntry::make('approved_at')
                                    ->label('Approved At')
                                    ->dateTime()
                                    ->visible(fn ($record) => in_array($record->status, ['approved', 'rejected'])),

                                Infolists\Components\TextEntry::make('admin_notes')
                                    ->label('Admin Notes')
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => !empty($record->admin_notes)),
                            ]),
                    ]),

                Infolists\Components\Section::make('RSS Information')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('last_rss_check')
                                    ->label('Last RSS Check')
                                    ->dateTime()
                                    ->visible(fn ($record) => $record->last_rss_check),

                                Infolists\Components\TextEntry::make('rss_error')
                                    ->label('RSS Error')
                                    ->color('danger')
                                    ->columnSpanFull()
                                    ->visible(fn ($record) => !empty($record->rss_error)),
                            ]),
                    ]),

                Infolists\Components\Section::make('Statistics')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('episodes_count')
                                    ->label('Episodes')
                                    ->numeric(),

                                Infolists\Components\TextEntry::make('reviews_count')
                                    ->label('Reviews')
                                    ->numeric(),

                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Submitted')
                                    ->dateTime(),
                            ]),
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
            'index' => Pages\ListPodcasts::route('/'),
            'create' => Pages\CreatePodcast::route('/create'),
            'view' => Pages\ViewPodcast::route('/{record}'),
            'edit' => Pages\EditPodcast::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['pending', 'verified'])->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::whereIn('status', ['pending', 'verified'])->count() > 0 ? 'warning' : null;
    }
} 