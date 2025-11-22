<?php

namespace App\Filament\Resources\Items\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Items\ItemResource;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Log Stock')
                ->url(route('filament.admin.resources.stock-logs.create'))
                ->openUrlInNewTab()
                ->label('Log new stock')
                ->icon('heroicon-o-plus-circle'),
            CreateAction::make(),
        ];
    }
}
