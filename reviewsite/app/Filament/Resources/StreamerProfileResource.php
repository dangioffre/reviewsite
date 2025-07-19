<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StreamerProfileResource\Pages;
use App\Models\StreamerProfile;
use App\Services\StreamerAuditService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class StreamerProfileResource extends Resource
{
    protected static ?string $model = StreamerProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationGroup = 'Streamers';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Streamer Profiles';

    protected static ?string $pluralModelLabel = 'Streamer Profiles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required()
                            ->disabled(fn (string $context): bool => $context === 'edit'),
                        Forms\Components\Select::make('platform')
                            ->options([
                                'twitch' => 'Twitch',
                                'youtube' => 'YouTube',
                                'kick' => 'Kick',
                            ])
                            ->required()
                            ->disabled(fn (string $context): bool => $context === 'edit'),
                        Forms\Components\TextInput::make('platform_user_id')
                            ->label('Platform User ID')
                            ->required()
                            ->disabled(fn (string $context): bool => $context === 'edit'),
                        Forms\Components\TextInput::make('channel_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('channel_url')
                            ->url()
                            ->required()
                            ->maxLength(500),
                        Forms\Components\TextInput::make('profile_photo_url')
                            ->url()
                            ->maxLength(500)
                            ->label('Profile Photo URL'),
                        Forms\Components\Textarea::make('bio')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Status & Verification')
                    ->schema([
                        Forms\Components\Toggle::make('is_approved')
                            ->label('Approved')
                            ->helperText('Whether this streamer profile is approved for public display'),
                        Forms\Components\Select::make('verification_status')
                            ->label('Verification Status')
                            ->options([
                                'pending' => 'Pending',
                                'requested' => 'Requested',
                                'in_review' => 'In Review',
                                'verified' => 'Verified',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending')
                            ->helperText('Current verification status of the streamer profile'),
                        Forms\Components\Textarea::make('verification_notes')
                            ->label('Verification Notes')
                            ->helperText('Internal notes about the verification process')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('OAuth Information')
                    ->schema([
                        Forms\Components\DateTimePicker::make('oauth_expires_at')
                            ->label('OAuth Token Expires At')
                            ->disabled(),
                    ])
                    ->collapsed()
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo_url')
                    ->label('Photo')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->channel_name)),
                Tables\Columns\TextColumn::make('channel_name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('platform')
                    ->colors([
                        'primary' => 'twitch',
                        'danger' => 'youtube',
                        'success' => 'kick',
                    ])
                    ->icons([
                        'heroicon-o-video-camera' => 'twitch',
                        'heroicon-o-play' => 'youtube',
                        'heroicon-o-bolt' => 'kick',
                    ]),
                Tables\Columns\IconColumn::make('is_approved')
                    ->boolean()
                    ->label('Approved')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\BadgeColumn::make('verification_status')
                    ->label('Verification')
                    ->colors([
                        'gray' => 'pending',
                        'yellow' => 'requested',
                        'blue' => 'in_review',
                        'success' => 'verified',
                        'danger' => 'rejected',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-paper-airplane' => 'requested',
                        'heroicon-o-eye' => 'in_review',
                        'heroicon-o-shield-check' => 'verified',
                        'heroicon-o-shield-exclamation' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('followers_count')
                    ->label('Followers')
                    ->counts('followers')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviews_count')
                    ->label('Reviews')
                    ->counts('reviews')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('platform')
                    ->options([
                        'twitch' => 'Twitch',
                        'youtube' => 'YouTube',
                        'kick' => 'Kick',
                    ]),
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Approved Status')
                    ->placeholder('All profiles')
                    ->trueLabel('Approved only')
                    ->falseLabel('Pending approval'),
                Tables\Filters\SelectFilter::make('verification_status')
                    ->label('Verification Status')
                    ->options([
                        'pending' => 'Pending',
                        'requested' => 'Requested',
                        'in_review' => 'In Review',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (StreamerProfile $record): bool => !$record->is_approved)
                    ->requiresConfirmation()
                    ->modalHeading('Approve Streamer Profile')
                    ->modalDescription('Are you sure you want to approve this streamer profile? It will become publicly visible.')
                    ->action(function (StreamerProfile $record) {
                        $record->update(['is_approved' => true]);
                        StreamerAuditService::logApproval($record);
                        
                        Notification::make()
                            ->title('Profile Approved')
                            ->body("Streamer profile for {$record->channel_name} has been approved.")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (StreamerProfile $record): bool => $record->is_approved)
                    ->requiresConfirmation()
                    ->modalHeading('Reject Streamer Profile')
                    ->modalDescription('Are you sure you want to reject this streamer profile? It will be hidden from public view.')
                    ->action(function (StreamerProfile $record) {
                        $record->update(['is_approved' => false]);
                        StreamerAuditService::logRejection($record);
                        
                        Notification::make()
                            ->title('Profile Rejected')
                            ->body("Streamer profile for {$record->channel_name} has been rejected.")
                            ->warning()
                            ->send();
                    }),
                Tables\Actions\Action::make('set_in_review')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->label('Set In Review')
                    ->visible(fn (StreamerProfile $record): bool => $record->verification_status === 'requested')
                    ->requiresConfirmation()
                    ->modalHeading('Set Verification In Review')
                    ->modalDescription('Mark this verification request as being reviewed.')
                    ->action(function (StreamerProfile $record) {
                        $record->setInReview();
                        StreamerAuditService::logVerificationStatusChange($record, 'in_review');
                        
                        Notification::make()
                            ->title('Status Updated')
                            ->body("Verification for {$record->channel_name} is now in review.")
                            ->info()
                            ->send();
                    }),
                Tables\Actions\Action::make('verify')
                    ->icon('heroicon-o-shield-check')
                    ->color('success')
                    ->visible(fn (StreamerProfile $record): bool => in_array($record->verification_status, ['requested', 'in_review']))
                    ->requiresConfirmation()
                    ->modalHeading('Verify Streamer Profile')
                    ->modalDescription('Are you sure you want to verify this streamer profile? This confirms channel ownership.')
                    ->form([
                        Forms\Components\Textarea::make('verification_notes')
                            ->label('Verification Notes')
                            ->placeholder('Optional notes about the verification...')
                            ->rows(3),
                    ])
                    ->action(function (StreamerProfile $record, array $data) {
                        $record->verify(auth()->user(), $data['verification_notes'] ?? null);
                        StreamerAuditService::logVerification($record);
                        
                        Notification::make()
                            ->title('Profile Verified')
                            ->body("Streamer profile for {$record->channel_name} has been verified.")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject_verification')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color('danger')
                    ->label('Reject')
                    ->visible(fn (StreamerProfile $record): bool => in_array($record->verification_status, ['requested', 'in_review']))
                    ->requiresConfirmation()
                    ->modalHeading('Reject Verification')
                    ->modalDescription('Are you sure you want to reject this verification request?')
                    ->form([
                        Forms\Components\Textarea::make('verification_notes')
                            ->label('Rejection Reason')
                            ->placeholder('Please provide a reason for rejection...')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (StreamerProfile $record, array $data) {
                        $record->rejectVerification(auth()->user(), $data['verification_notes']);
                        StreamerAuditService::logVerificationRejection($record);
                        
                        Notification::make()
                            ->title('Verification Rejected')
                            ->body("Verification for {$record->channel_name} has been rejected.")
                            ->warning()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Streamer Profile')
                    ->modalDescription('Are you sure you want to delete this streamer profile? This action cannot be undone.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Approve Selected Profiles')
                        ->modalDescription('Are you sure you want to approve all selected streamer profiles?')
                        ->action(function ($records) {
                            $count = $records->count();
                            $records->each(function ($record) {
                                $record->update(['is_approved' => true]);
                                StreamerAuditService::logApproval($record);
                            });
                            
                            Notification::make()
                                ->title('Profiles Approved')
                                ->body("{$count} streamer profiles have been approved.")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Reject Selected Profiles')
                        ->modalDescription('Are you sure you want to reject all selected streamer profiles?')
                        ->action(function ($records) {
                            $count = $records->count();
                            $records->each(function ($record) {
                                $record->update(['is_approved' => false]);
                                StreamerAuditService::logRejection($record);
                            });
                            
                            Notification::make()
                                ->title('Profiles Rejected')
                                ->body("{$count} streamer profiles have been rejected.")
                                ->warning()
                                ->send();
                        }),
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
            'index' => Pages\ListStreamerProfiles::route('/'),
            'create' => Pages\CreateStreamerProfile::route('/create'),
            'edit' => Pages\EditStreamerProfile::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'followers', 'reviews']);
    }
}