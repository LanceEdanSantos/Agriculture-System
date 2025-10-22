<?php

namespace App\Filament\Exports;

use App\Models\InventoryItem;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InventoryItemExporter extends Exporter
{
    protected static ?string $model = InventoryItem::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Item Name'),
            ExportColumn::make('item_code')->label('Item Code'),
            ExportColumn::make('description')->label('Description')->limit(100),
            ExportColumn::make('category.name')->label('Category'),
            ExportColumn::make('unit.name')->label('Unit'),
            ExportColumn::make('current_stock')->label('Current Stock'),
            // ExportColumn::make('minimum_stock')->label('Minimum Stock'),
            // ExportColumn::make('unit_cost')
            //     ->label('Unit Cost')
            //     ->formatStateUsing(fn($state) => '₱' . number_format($state, 2)),
            // ExportColumn::make('total_stock_value')
            //     ->label('Total Value')
            //     ->state(fn(InventoryItem $record) => $record->current_stock * $record->unit_cost)
            //     ->formatStateUsing(fn($state) => '₱' . number_format($state, 2)),
            ExportColumn::make('supplier.name')->label('Supplier'),
            ExportColumn::make('last_purchase_date')->label('Last Purchase Date'),
            ExportColumn::make('expiration_date')->label('Expiration Date'),
            ExportColumn::make('notes')->label('Notes')->limit(200),
            ExportColumn::make('created_at')->label('Date Added'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your inventory export completed with ' . number_format($export->successful_rows) . ' '
            . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failed = $export->getFailedRowsCount()) {
            $body .= " ⚠️ {$failed} " . str('row')->plural($failed) . ' failed to export.';
        }

        return $body;
    }
}
