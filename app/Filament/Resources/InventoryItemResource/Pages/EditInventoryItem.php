<?php

namespace App\Filament\Resources\InventoryItemResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\InventoryItemResource;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EditInventoryItem extends EditRecord
{
    protected static string $resource = InventoryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];

        
    }
    protected function resolveRecord(int|string $key): Model
    {
        // Allow viewing even if the record is soft deleted
        return static::getResource()::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->withTrashed()
            ->findOrFail($key);
    }
    protected function getRedirectUrl(): string
    {
        // Redirect to the table (index) after saving
        return $this->getResource()::getUrl('index');
    }
}
