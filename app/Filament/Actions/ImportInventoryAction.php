<?php

namespace App\Filament\Actions;

use App\Models\InventoryItem;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;

class ImportInventoryAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'import_inventory';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Import from JSON')
            ->icon('heroicon-o-arrow-up-tray')
            ->form([
                Textarea::make('json_data')
                    ->label('JSON Data')
                    ->required()
                    ->rows(10)
                    ->placeholder('Paste your JSON data here...')
                    ->helperText('Paste the purchase request JSON data to import items into inventory'),
            ])
            ->action(function (array $data): void {
                $this->importFromJson($data['json_data']);
            });
    }

    protected function importFromJson(string $jsonData): void
    {
        try {
            $data = json_decode($jsonData, true);

            if (!$data || !isset($data['purchase_request']['sections'])) {
                throw new \Exception('Invalid JSON format. Expected purchase_request with sections.');
            }

            $importedCount = 0;
            $updatedCount = 0;

            foreach ($data['purchase_request']['sections'] as $section) {
                $category = $section['category'] ?? 'Other';

                foreach ($section['items'] as $item) {
                    $description = $item['description'] ?? '';
                    $unit = $item['unit'] ?? 'pcs';
                    $unitCost = $item['unit_cost'] ?? 0;

                    // Try to find existing item by description
                    $existingItem = InventoryItem::where('description', $description)->first();

                    if ($existingItem) {
                        // Update existing item
                        $existingItem->update([
                            'unit_cost' => $unitCost,
                            'category' => $category,
                            'unit' => $unit,
                        ]);
                        $updatedCount++;
                    } else {
                        // Create new item
                        InventoryItem::create([
                            'name' => $this->extractItemName($description),
                            'description' => $description,
                            'category' => $category,
                            'unit' => $unit,
                            'unit_cost' => $unitCost,
                            'current_stock' => 0,
                            'minimum_stock' => 10,
                            'status' => 'active',
                        ]);
                        $importedCount++;
                    }
                }
            }

            Notification::make()
                ->title('Import Successful')
                ->body("Imported {$importedCount} new items and updated {$updatedCount} existing items.")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Import Failed')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function extractItemName(string $description): string
    {
        // Extract a shorter name from the description
        $parts = explode(' - ', $description);
        return $parts[0] ?? $description;
    }
}
