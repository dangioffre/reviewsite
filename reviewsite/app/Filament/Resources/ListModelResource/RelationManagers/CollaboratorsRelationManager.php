<?php

namespace App\Filament\Resources\ListModelResource\RelationManagers;

use App\Models\User;
use App\Models\ListCollaborator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CollaboratorsRelationManager extends RelationManager
{
    protected static string $relationship = 'collaborators';
    
    protected static ?string $recordTitleAttribute = 'user.name';
    
    protected static ?string $title = 'Collaborators';
    
    protected static ?string $modelLabel = 'Collaborator';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Collaborator Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                            ]),
                            
                        Forms\Components\Toggle::make('invited_by_owner')
                            ->label('Invited by List Owner')
                            ->default(true)
                            ->helperText('Whether this collaboration was initiated by the list owner'),
                            
                        Forms\Components\DateTimePicker::make('invited_at')
                            ->label('Invitation Date')
                            ->default(now())
                            ->required(),
                            
                        Forms\Components\DateTimePicker::make('accepted_at')
                            ->label('Accepted Date')
                            ->helperText('Leave empty if invitation is still pending'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Permissions')
                    ->schema([
                        Forms\Components\Toggle::make('can_add_games')
                            ->label('Can Add Games')
                            ->helperText('Allow this collaborator to add games to the list')
                            ->default(true),
                            
                        Forms\Components\Toggle::make('can_delete_games')
                            ->label('Can Remove Games')
                            ->helperText('Allow this collaborator to remove games from the list')
                            ->default(false),
                            
                        Forms\Components\Toggle::make('can_rename_list')
                            ->label('Can Rename List')
                            ->helperText('Allow this collaborator to change the list name and description')
                            ->default(false),
                            
                        Forms\Components\Toggle::make('can_manage_users')
                            ->label('Can Manage Users')
                            ->helperText('Allow this collaborator to invite/remove other collaborators')
                            ->default(false),
                            
                        Forms\Components\Toggle::make('can_change_privacy')
                            ->label('Can Change Privacy')
                            ->helperText('Allow this collaborator to make the list public/private')
                            ->default(false),
                            
                        Forms\Components\Toggle::make('can_change_category')
                            ->label('Can Change Category')
                            ->helperText('Allow this collaborator to change the list category')
                            ->default(false),
                    ])->columns(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Collaborator')
                    ->searchable()
                    ->sortable()
                    ->description(fn (ListCollaborator $record): string => $record->user->email),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (ListCollaborator $record): string => 
                        $record->accepted_at ? 'Active' : 'Pending'
                    )
                    ->colors([
                        'success' => fn (ListCollaborator $record): bool => $record->accepted_at !== null,
                        'warning' => fn (ListCollaborator $record): bool => $record->accepted_at === null,
                    ])
                    ->icons([
                        'heroicon-m-check-circle' => fn (ListCollaborator $record): bool => $record->accepted_at !== null,
                        'heroicon-m-clock' => fn (ListCollaborator $record): bool => $record->accepted_at === null,
                    ])
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('can_add_games')
                    ->label('Add Games')
                    ->boolean()
                    ->tooltip('Can Add Games'),
                    
                Tables\Columns\IconColumn::make('can_delete_games')
                    ->label('Remove Games')
                    ->boolean()
                    ->tooltip('Can Remove Games'),
                    
                Tables\Columns\IconColumn::make('can_rename_list')
                    ->label('Rename')
                    ->boolean()
                    ->tooltip('Can Rename List'),
                    
                Tables\Columns\IconColumn::make('can_manage_users')
                    ->label('Manage Users')
                    ->boolean()
                    ->tooltip('Can Manage Other Collaborators'),
                    
                Tables\Columns\IconColumn::make('can_change_privacy')
                    ->label('Privacy')
                    ->boolean()
                    ->tooltip('Can Change Privacy Settings'),
                    
                Tables\Columns\IconColumn::make('can_change_category')
                    ->label('Category')
                    ->boolean()
                    ->tooltip('Can Change List Category'),
                    
                Tables\Columns\TextColumn::make('invited_at')
                    ->label('Invited')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('accepted_at')
                    ->label('Accepted')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\BadgeColumn::make('invited_by_owner')
                    ->label('Invited By')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Owner' : 'User')
                    ->colors([
                        'primary' => fn (bool $state): bool => $state,
                        'secondary' => fn (bool $state): bool => !$state,
                    ])
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'pending' => 'Pending',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'active' => $query->whereNotNull('accepted_at'),
                            'pending' => $query->whereNull('accepted_at'),
                            default => $query,
                        };
                    }),
                    
                Tables\Filters\Filter::make('invited_by_owner')
                    ->label('Invited by Owner')
                    ->query(fn (Builder $query): Builder => $query->where('invited_by_owner', true)),
                    
                Tables\Filters\Filter::make('has_admin_permissions')
                    ->label('Admin Permissions')
                    ->query(fn (Builder $query): Builder => $query->where('can_manage_users', true)),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['invited_at'] = $data['invited_at'] ?? now();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('accept')
                    ->label('Accept')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->action(fn (ListCollaborator $record) => $record->update(['accepted_at' => now()]))
                    ->visible(fn (ListCollaborator $record): bool => $record->accepted_at === null)
                    ->requiresConfirmation()
                    ->modalDescription('This will mark the collaboration as accepted.'),
                    
                Tables\Actions\Action::make('revoke')
                    ->label('Revoke')
                    ->icon('heroicon-m-x-mark')
                    ->color('warning')
                    ->action(fn (ListCollaborator $record) => $record->update(['accepted_at' => null]))
                    ->visible(fn (ListCollaborator $record): bool => $record->accepted_at !== null)
                    ->requiresConfirmation()
                    ->modalDescription('This will revoke the collaboration and make it pending again.'),
                    
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('accept_all')
                        ->label('Accept All')
                        ->icon('heroicon-m-check')
                        ->color('success')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['accepted_at' => now()])))
                        ->requiresConfirmation(),
                        
                    Tables\Actions\BulkAction::make('revoke_all')
                        ->label('Revoke All')
                        ->icon('heroicon-m-x-mark')
                        ->color('warning')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['accepted_at' => null])))
                        ->requiresConfirmation(),
                        
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('invited_at', 'desc');
    }
}
