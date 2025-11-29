<?php

namespace App\Filament\Exports;

use App\Models\Item;
use Illuminate\Support\Number;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class ItemExporter extends Exporter
{
    protected static ?string $model = Item::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('slug')
                ->label('Slug'),
            ExportColumn::make('name')
                ->label('Name'),
            ExportColumn::make('category.name')
                ->label('Category'),
            ExportColumn::make('stock')
                ->label('Stock'),
            ExportColumn::make('minimum_stock')
                ->label('Minimum Stock'),
            ExportColumn::make('description')
                ->label('Description'),
            ExportColumn::make('notes')
                ->label('Notes'),
            ExportColumn::make('active')
                ->label('Active')
                ->formatStateUsing(fn(string $state): string => $state ? 'Yes' : 'No'),
            ExportColumn::make('unit')
                ->label('Unit'),
            ExportColumn::make('quantity')
                ->label('Quantity'),
            ExportColumn::make('created_at')
                ->label('Created At'),
            ExportColumn::make('updated_at')
                ->label('Updated At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your item export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
