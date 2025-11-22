<?php

namespace App\Filament\Resources\Farms\RelationManagers;

use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\RelationManagers\RelationManager;

class ItemsRelationManager extends RelationManager
{
    // Must match your Farm model's belongsToMany relationship
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'name';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Add Item')
                    ->preloadRecordSelect() // Preload item list
                    ->multiple(),           // Attach multiple items
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('View Item'),        // View item
                    DetachAction::make()
                        ->label('Remove Item'), 
            ])   // Remove from pivot
            ])
            ->toolbarActions([
                DetachBulkAction::make()
                    ->label('Remove Items'),  // Bulk detach
            ]);
    }
    public function isReadOnly(): bool
    {
        return false;
}
}
