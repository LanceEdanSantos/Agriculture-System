<?php

namespace App\Filament\Resources\StockMovementResource\Pages;

use App\Filament\Resources\StockMovementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStockMovement extends CreateRecord
{
    protected static string $resource = StockMovementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the user ID to the currently authenticated user
        $data['user_id'] = auth()->id();

        // Calculate total cost if unit cost is provided
        if (isset($data['unit_cost']) && isset($data['quantity'])) {
            $data['total_cost'] = $data['unit_cost'] * $data['quantity'];
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}