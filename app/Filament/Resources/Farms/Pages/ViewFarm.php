<?php

namespace App\Filament\Resources\Farms\Pages;

use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\EditAction;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Farms\FarmResource;
use Filament\Support\Icons\Heroicon;

class ViewFarm extends ViewRecord
{
    protected static string $resource = FarmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportUsers')
                ->label('Export Users to PDF')
                ->icon(Heroicon::ArrowDownCircle)
                ->action(function ($record) {

                    // Get users for this farm
                    $users = $record->users()->get();

                    // Generate PDF view
                    $pdf = Pdf::loadView('pdf.farm_users', [
                        'farm' => $record,
                        'users' => $users,
                    ]);

                    // Save to storage (optional)
                    $fileName = 'farm_users_' . $record->id . '_' . now()->format('Ymd_His') . '.pdf';
                    $path = 'exports/' . $fileName;
                    Storage::disk('public')->put($path, $pdf->output());

                    // Return PDF as download response
                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        $fileName
                    );
                })
                ->requiresConfirmation()
                ->color('primary'),
            EditAction::make(),
        ];
    }
}
