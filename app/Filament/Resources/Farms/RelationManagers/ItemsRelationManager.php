<?php

namespace App\Filament\Resources\Farms\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
                ViewAction::make(),        // View item
                DetachAction::make()
                    ->label('Remove Item'),      // Remove from pivot
            ])
            ->toolbarActions([
                DetachBulkAction::make(),  // Bulk detach
            ]);
    }
    public function isReadOnly(): bool
    {
        return false;
    }
}
