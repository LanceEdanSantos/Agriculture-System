<?php

namespace App\Filament\Resources\Farms\Pages;

use App\Filament\Resources\Farms\FarmResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditFarm extends EditRecord
{
    protected static string $resource = FarmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
