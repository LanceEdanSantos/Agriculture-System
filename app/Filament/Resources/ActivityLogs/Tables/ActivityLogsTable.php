<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')
                    ->label('Log Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->sortable(),
                // TextColumn::make('subject_type')
                //     ->label('Subject Type')
                //     ->searchable()
                //     ->sortable(),
                // TextColumn::make('causer_type')
                //     ->label('Causer Type')
                //     ->searchable()
                //     ->sortable(),
                TextColumn::make('properties')
                    ->badge(true)
                    ->words(3, end: ' (Hidden for security reasons)')
                    ->label('Properties')
                    ->getStateUsing(fn ($record) => 
                        isset($record->properties['attributes']) 
                            ? json_encode($record->properties['attributes']) 
                            : 'Hidden'
                    )
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->filters([
                // TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                // EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    // ForceDeleteBulkAction::make(),
                    // RestoreBulkAction::make(),
                ]),
            ]);
    }
}
