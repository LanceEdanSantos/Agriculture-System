<?php

namespace App\Filament\Resources\StockMovementResource\Pages;

use App\Filament\Resources\StockMovementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockMovements extends ListRecords
{
    protected static string $resource = StockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data) {
                    // Set the logged-in user ID using the same logic as your CreateStockMovement page
                    $data['user_id'] = auth()->id();

                    // Optional: keep your unit_cost / total_cost logic if needed
                    if (isset($data['unit_cost']) && isset($data['quantity'])) {
                        $data['total_cost'] = $data['unit_cost'] * $data['quantity'];
                    }

                    return $data;
                }),
        ];
    }
}