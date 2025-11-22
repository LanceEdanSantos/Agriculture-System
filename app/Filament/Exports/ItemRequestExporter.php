<?php

namespace App\Filament\Exports;

use App\Models\ItemRequest;
use Illuminate\Support\Number;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class ItemRequestExporter extends Exporter
{
    protected static ?string $model = ItemRequest::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('item.name')
                ->label('Item'),
            ExportColumn::make('quantity')
                ->label('Quantity'),
            ExportColumn::make('created_at')
                ->label('Created At'),
            ExportColumn::make('farm.name')
                ->label('Farm'),
            ExportColumn::make('user.name')
                ->label('Requester'),
            ExportColumn::make('status')
                ->label('Status'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your item request export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
