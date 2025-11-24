<?php

namespace App\Filament\Resources\ActivityLogs\Pages;

use Illuminate\Support\Arr;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\ActivityLogs\ActivityLogResource;

class ViewActivityLog extends ViewRecord
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('restore')
                ->label('Restore')
                // ->icon('heroicon-o-refresh')
                ->color('success')
                ->requiresConfirmation()
                // ->visible(fn($record) => $record->trashed()) // only show if soft-deleted
                ->action(function ($record) {

                    // The model affected
                    $modelClass = $record->subject_type;
                    $modelId    = $record->subject_id;

                    $model = $modelClass::find($modelId);

                    if (! $model) {
                        throw new \Exception('Model no longer exists.');
                    }

                    // Extract old values from the Spatie activity log
                    $old = Arr::get($record->properties, 'old', []);

                    if (! is_array($old) || empty($old)) {
                        throw new \Exception('This activity does not contain old values.');
                    }

                    // Apply old values to the model
                    $model->fill($old);
                    $model->save();

                    // Optional: create a new activity log entry
                    // activity()
                    //     ->performedOn($model)
                    //     ->logName($record->log_name)
                    //     ->withProperties(['reverted_from_activity' => $record->id])
                    //     ->log('reverted');
                })
        ];
    }
}
