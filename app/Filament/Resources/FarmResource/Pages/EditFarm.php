<?php

namespace App\Filament\Resources\FarmResource\Pages;

use App\Filament\Resources\FarmResource;
use App\Filament\Exports\FarmExporter;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFarm extends EditRecord
{
    protected static string $resource = FarmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Print Report')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn () => route('farms.print', ['id' => $this->record->id]))
                ->openUrlInNewTab(),
            Actions\ExportAction::make()
                ->exporter(FarmExporter::class)
                ->label('Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success'),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
