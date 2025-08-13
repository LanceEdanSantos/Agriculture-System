<?php

namespace App\Filament\Resources\PurchaseRequestResource\Pages;

use App\Filament\Resources\PurchaseRequestResource;
use App\Models\InventoryItem;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPurchaseRequest extends EditRecord
{
    protected static string $resource = PurchaseRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Auto-number items
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $index => $item) {
                if (!isset($item['item_no']) || $item['item_no'] === null) {
                    $data['items'][$index]['item_no'] = $index + 1;
                }
            }
        }

        // Update the purchase request
        $record->update($data);

        // Handle adding custom items to inventory
        if (isset($data['items'])) {
            foreach ($data['items'] as $itemData) {
                if (isset($itemData['add_to_inventory']) && $itemData['add_to_inventory'] && $itemData['is_custom_item']) {
                    // Check if inventory item already exists
                    $existingItem = InventoryItem::where('name', $itemData['description'])
                        ->where('item_code', $itemData['item_code'] ?? null)
                        ->first();

                    if (!$existingItem) {
                        // Create inventory item from custom item data
                        $inventoryItemData = [
                            'name' => $itemData['description'] ?? '',
                            'item_code' => $itemData['item_code'] ?? null,
                            'description' => $itemData['description'] ?? '',
                            'category_id' => null, // You might want to set a default category or get it from the form
                            'unit_id' => null, // You might want to set a default unit or get it from the form
                            'unit_cost' => $itemData['unit_cost'] ?? 0,
                            'current_stock' => 0,
                            'minimum_stock' => 0,
                            'status' => 'active',
                        ];

                        // Create the inventory item
                        InventoryItem::create($inventoryItemData);
                    }
                }
            }
        }

        return $record;
    }
}
