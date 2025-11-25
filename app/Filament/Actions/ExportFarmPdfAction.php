<?php

namespace App\Filament\Actions;

use App\Models\Farm;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Contracts\View\View;

class ExportFarmPdfAction extends Action
{
    use CanCustomizeProcess;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Export PDF');
        $this->icon('heroicon-o-document-arrow-down');
        $this->color('success');

        $this->action(function (Farm $record) {
            $pdf = Pdf::loadHTML(
                Blade::render('pdf.farm', [
                    'record' => $record,
                ])
            );

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'farm-details-' . $record->id . '.pdf');
        });
    }
}
