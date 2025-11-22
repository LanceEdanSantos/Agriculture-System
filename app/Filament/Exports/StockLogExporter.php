<?php

namespace App\Filament\Exports;

use App\Models\StockLog;
use Illuminate\Support\Number;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class StockLogExporter extends Exporter
{
    protected static ?string $model = StockLog::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID')
                ->sortable(),
            ExportColumn::make('item.name')
                ->label('Item')
                ->sortable(),
            ExportColumn::make('quantity')
                ->label('Quantity')
                ->sortable(),
            ExportColumn::make('created_at')
                ->label('Created At')
                ->sortable(),
            ExportColumn::make('farm.name')
                ->label('Farm')
                ->sortable(),
            ExportColumn::make('user.name')
                ->label('Requester')
                ->sortable(),
            ExportColumn::make('status')
                ->label('Status')
                ->sortable(),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your stock log export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
