<?php

namespace App\Filament\Resources\Farms\RelationManagers;

use Filament\Actions\ActionGroup;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersRelationManager extends RelationManager
{
    // Must match your Farm model's belongsToMany relationship method
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'name';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Add User')
                    ->preloadRecordSelect() // Preload user list
                    ->multiple(),           // Attach multiple users at once
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    DetachAction::make()
                        ->label('Remove User'),
                ]),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),   // Detach multiple users at once
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
