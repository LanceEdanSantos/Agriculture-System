<?php

namespace App\Filament\Resources\CustomActivityLogResource\Pages;

use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\CustomActivityLogResource;
use Rmsramos\Activitylog\Resources\ActivitylogResource;

class ViewCustomActivityLogs extends ViewRecord
{
    public static function getResource(): string
    {
        return CustomActivityLogResource::class;
    }

    protected function getHeaderActions(): array
    {
        // Get parent actions from the base resource (if any)
        $actions = method_exists(ActivitylogResource::class, 'getRecordActions')
            ? ActivitylogResource::getRecordActions()
            : [];

        // ✅ Force add restore button manually (always visible for testing)
        $actions[] = Action::make('restore')
            ->label('Redo / Restore')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('primary')
            ->requiresConfirmation()
            ->visible(fn() => true)
            ->action(function () {
                // You can use the vendor restore logic or your own
                $this->record->subject?->fill(
                    data_get($this->record->properties, 'old', [])
                )?->save();

                $this->notify('success', 'Record restored successfully.');
            });

        return $actions;
    }
}
