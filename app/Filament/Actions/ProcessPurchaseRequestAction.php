<?php

namespace App\Filament\Actions;

use App\Models\InventoryItem;
use App\Models\PurchaseHistory;
use App\Models\Supplier;
use App\Models\Unit;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\KeyValue;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProcessPurchaseRequestAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'process_purchase_request';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Process Purchase Request')
            ->icon('heroicon-o-truck')
            ->color('success')
            ->form([
                Section::make('Delivery Information')
                    ->schema([
                        DatePicker::make('delivery_date')
                            ->label('Delivery Date')
                            ->required()
                            ->default(now()),
                        TextInput::make('purchase_order_number')
                            ->label('Purchase Order Number')
                            ->maxLength(50),
                        TextInput::make('invoice_number')
                            ->label('Invoice Number')
                            ->maxLength(50),
                    ]),

                Section::make('Supplier Information')
                    ->schema([
                        Select::make('supplier_id')
                            ->label('Select Supplier')
                            ->options(Supplier::where('status', 'active')->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Toggle::make('add_new_supplier')
                            ->label('Add New Supplier')
                            ->default(false),
                        Repeater::make('new_supplier')
                            ->label('New Supplier Details')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Supplier Name')
                                    ->required(),
                                TextInput::make('company_name')
                                    ->label('Company Name'),
                                Repeater::make('contact_persons')
                                    ->label('Contact Persons')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Contact Person Name')
                                            ->required(),
                                        TextInput::make('position')
                                            ->label('Position'),
                                    ]),
                                Repeater::make('phone_numbers')
                                    ->label('Phone Numbers')
                                    ->schema([
                                        TextInput::make('number')
                                            ->label('Phone Number')
                                            ->tel()
                                            ->required(),
                                    ]),
                                Repeater::make('email_addresses')
                                    ->label('Email Addresses')
                                    ->schema([
                                        TextInput::make('email')
                                            ->label('Email Address')
                                            ->email()
                                            ->required(),
                                    ]),
                                Textarea::make('address')
                                    ->label('Address')
                                    ->rows(3),
                                TextInput::make('website')
                                    ->label('Website'),
                                TextInput::make('tax_id')
                                    ->label('Tax ID'),
                                TextInput::make('business_license')
                                    ->label('Business License'),
                                Textarea::make('notes')
                                    ->label('Notes')
                                    ->rows(3),
                            ])
                            ->visible(fn($get) => $get('add_new_supplier'))
                            ->collapsible(),
                    ]),

                Section::make('Items Processing')
                    ->schema([
                        Repeater::make('items')
                            ->label('Process Items')
                            ->schema([
                                TextInput::make('description')
                                    ->label('Item Description')
                                    ->disabled(),
                                Select::make('action')
                                    ->label('Action')
                                    ->options([
                                        'update_existing' => 'Update Existing Item',
                                        'create_new' => 'Create New Item',
                                    ])
                                    ->required(),
                                Select::make('inventory_item_id')
                                    ->label('Select Existing Item')
                                    ->options(InventoryItem::pluck('name', 'id'))
                                    ->searchable()
                                    ->visible(fn($get) => $get('action') === 'update_existing'),
                                TextInput::make('new_item_name')
                                    ->label('New Item Name')
                                    ->visible(fn($get) => $get('action') === 'create_new'),

                                // Unit selection with custom option
                                Select::make('unit_selection')
                                    ->label('Unit Selection')
                                    ->options([
                                        'existing' => 'Select Existing Unit',
                                        'custom' => 'Add Custom Unit',
                                    ])
                                    ->default('existing')
                                    ->required(),
                                Select::make('unit_id')
                                    ->label('Select Unit')
                                    ->options(Unit::active()->pluck('name', 'id'))
                                    ->searchable()
                                    ->visible(fn($get) => $get('unit_selection') === 'existing'),
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('custom_unit_name')
                                            ->label('Custom Unit Name')
                                            ->visible(fn($get) => $get('unit_selection') === 'custom'),
                                        TextInput::make('custom_unit_abbreviation')
                                            ->label('Unit Abbreviation')
                                            ->visible(fn($get) => $get('unit_selection') === 'custom'),
                                    ])
                                    ->visible(fn($get) => $get('unit_selection') === 'custom'),

                                TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->required(),
                                TextInput::make('unit_cost')
                                    ->label('Unit Cost (₱)')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->required(),
                                DatePicker::make('expiration_date')
                                    ->label('Expiration Date'),
                                Textarea::make('notes')
                                    ->label('Notes')
                                    ->rows(2),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['description'] ?? null),
                    ]),
            ])
            ->action(function (array $data): void {
                $this->processPurchaseRequest($data);
            });
    }

    protected function processPurchaseRequest(array $data): void
    {
        DB::transaction(function () use ($data) {
            $supplierId = $data['supplier_id'];

            // Create new supplier if needed
            if ($data['add_new_supplier'] && !empty($data['new_supplier'])) {
                $newSupplier = $data['new_supplier'][0];
                $supplier = Supplier::create([
                    'name' => $newSupplier['name'],
                    'company_name' => $newSupplier['company_name'] ?? null,
                    'contact_persons' => collect($newSupplier['contact_persons'] ?? [])->pluck('name')->toArray(),
                    'phone_numbers' => collect($newSupplier['phone_numbers'] ?? [])->pluck('number')->toArray(),
                    'email_addresses' => collect($newSupplier['email_addresses'] ?? [])->pluck('email')->toArray(),
                    'address' => $newSupplier['address'] ?? null,
                    'website' => $newSupplier['website'] ?? null,
                    'tax_id' => $newSupplier['tax_id'] ?? null,
                    'business_license' => $newSupplier['business_license'] ?? null,
                    'notes' => $newSupplier['notes'] ?? null,
                    'status' => 'active',
                ]);
                $supplierId = $supplier->id;
            }

            $processedItems = 0;
            $updatedItems = 0;
            $createdItems = 0;

            foreach ($data['items'] as $item) {
                // Handle unit creation or selection
                $unitId = null;
                $unitName = '';

                if ($item['unit_selection'] === 'custom') {
                    // Create custom unit
                    $unit = Unit::create([
                        'name' => $item['custom_unit_name'],
                        'abbreviation' => $item['custom_unit_abbreviation'] ?? $item['custom_unit_name'],
                        'category' => 'custom',
                        'description' => 'Custom unit created during purchase processing',
                        'is_custom' => true,
                        'status' => 'active',
                    ]);
                    $unitId = $unit->id;
                    $unitName = $unit->name;
                } else {
                    $unit = Unit::find($item['unit_id']);
                    $unitId = $unit->id;
                    $unitName = $unit->name;
                }

                $purchaseHistory = PurchaseHistory::create([
                    'supplier_id' => $supplierId,
                    'item_description' => $item['description'],
                    'category' => $this->extractCategory($item['description']),
                    'unit' => $unitName,
                    'quantity_purchased' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'total_cost' => $item['quantity'] * $item['unit_cost'],
                    'purchase_date' => now(),
                    'delivery_date' => $data['delivery_date'],
                    'expiration_date' => $item['expiration_date'] ?? null,
                    'purchase_order_number' => $data['purchase_order_number'] ?? null,
                    'invoice_number' => $data['invoice_number'] ?? null,
                    'status' => 'delivered',
                    'notes' => $item['notes'] ?? null,
                    'received_by' => Auth::user()->name,
                    'received_at' => now(),
                ]);

                if ($item['action'] === 'update_existing') {
                    // Update existing inventory item
                    $inventoryItem = InventoryItem::find($item['inventory_item_id']);
                    $inventoryItem->update([
                        'current_stock' => $inventoryItem->current_stock + $item['quantity'],
                        'unit_cost' => $item['unit_cost'],
                        'supplier_id' => $supplierId,
                        'unit_id' => $unitId,
                        'last_purchase_date' => now(),
                        'last_supplier' => Supplier::find($supplierId)->name,
                    ]);

                    $purchaseHistory->update(['inventory_item_id' => $inventoryItem->id]);
                    $inventoryItem->updateAverageUnitCost();
                    $inventoryItem->updateTotalPurchased();
                    $updatedItems++;
                } else {
                    // Create new inventory item
                    $inventoryItem = InventoryItem::create([
                        'name' => $item['new_item_name'],
                        'description' => $item['description'],
                        'category' => $this->extractCategory($item['description']),
                        'unit' => $unitName,
                        'unit_cost' => $item['unit_cost'],
                        'current_stock' => $item['quantity'],
                        'minimum_stock' => 10,
                        'supplier_id' => $supplierId,
                        'unit_id' => $unitId,
                        'average_unit_cost' => $item['unit_cost'],
                        'total_purchased' => $item['quantity'],
                        'last_purchase_date' => now(),
                        'last_supplier' => Supplier::find($supplierId)->name,
                        'expiration_date' => $item['expiration_date'] ?? null,
                        'status' => 'active',
                    ]);

                    $purchaseHistory->update(['inventory_item_id' => $inventoryItem->id]);
                    $createdItems++;
                }

                $processedItems++;
            }

            Notification::make()
                ->title('Purchase Request Processed Successfully')
                ->body("Processed {$processedItems} items: {$updatedItems} updated, {$createdItems} created.")
                ->success()
                ->send();
        });
    }

    protected function extractCategory(string $description): string
    {
        // Extract category from description or default to 'Other'
        $categories = [
            'seed' => ['seed', 'seeds'],
            'fertilizer' => ['fertilizer', 'fertilizers'],
            'pesticide' => ['pesticide', 'pesticides'],
            'tool' => ['tool', 'tools', 'equipment'],
            'plastic' => ['plastic', 'bag', 'bags'],
            'organic' => ['organic', 'vermicast'],
        ];

        $description = strtolower($description);

        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($description, $keyword)) {
                    return ucfirst($category);
                }
            }
        }

        return 'Other';
    }
}
