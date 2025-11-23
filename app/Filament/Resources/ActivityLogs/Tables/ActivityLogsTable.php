<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use App\Models\User;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Spatie\Activitylog\Models\Activity;
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
                TextColumn::make('causer.name')
                    ->label('Responsible')
                    ->state(function (Activity $record): string {
                        // Prefer the loaded relationship; if missing, attempt to find by causer_id
                        $user = $record->causer ?? (isset($record->causer_id) ? User::find($record->causer_id) : null);

                        if (! $user) {
                            return 'System';
                        }

                        $parts = array_filter([
                            Str::headline($user->first_name ?? ''),
                            $user->middle_name ? Str::title($user->middle_name) : null,
                            $user->last_name ? Str::title($user->last_name) : null,
                            $user->suffix ? Str::upper($user->suffix) : null,
                        ]);

                        return trim(implode(' ', $parts));
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('properties')
                    ->badge(true)
                    ->words(3, end: ' (Hidden for security reasons)')
                    ->label('Properties')
                    ->getStateUsing(function ($record) {
                        $data = $record->properties['attributes'] ?? null;

                        if (! $data) {
                            return 'Hidden';
                        }

                        $json = collect($data)
                            ->take(3) // show only first 3 keys
                            ->map(fn($value, $key) => "{$key}: {$value}")
                            ->implode(', ');

                        return $json . (count($data) > 3 ? ' ...' : '');
                    })
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
