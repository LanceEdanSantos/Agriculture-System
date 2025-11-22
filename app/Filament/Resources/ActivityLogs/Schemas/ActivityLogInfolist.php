<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ActivityLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                        'xl' => 2,
                    ])
                    ->schema([

                        // ---------------------------
                        // Activity Details
                        // ---------------------------
                        Section::make('Activity Details')
                            ->description('Basic information about this log entry')
                            ->schema([
                                TextEntry::make('id')->label('Log ID'),
                                TextEntry::make('causer.name')->label('Performed By')->placeholder('System'),
                                TextEntry::make('event')->label('Event'),
                                TextEntry::make('created_at')
                                    ->label('Time')
                                    ->getStateUsing(fn($record) => optional($record->created_at)?->format('M j, Y g:i A') ?? 'N/A'),
                            ])
                            ->columnSpanFull(),

                        // ---------------------------
                        // New Values
                        // ---------------------------
                        Section::make('New Values')
                            ->description('The updated values after this action')
                            ->schema([
                                TextEntry::make('attributes')
                                    ->label('Attributes')
                                    ->getStateUsing(function ($record) {
                                        $attributes = $record->properties['attributes'] ?? [];
                                        if (empty($attributes)) {
                                            return 'Hidden';
                                        }

                                        $lines = [];
                                        foreach ($attributes as $key => $value) {
                                            if ($value instanceof \DateTimeInterface) {
                                                $value = $value->format('M j, Y g:i A');
                                            } elseif (is_string($value) && strtotime($value) !== false) {
                                                $time = strtotime($value);
                                                if ($time !== false) {
                                                    $value = date('M j, Y g:i A', $time);
                                                }
                                            } elseif (is_bool($value)) {
                                                $value = $value ? 'Yes' : 'No';
                                            } elseif (is_null($value)) {
                                                $value = 'None';
                                            }
                                            $lines[] = "<strong>" . ucwords(str_replace('_', ' ', $key)) . ":</strong> " . $value;
                                        }

                                        return implode("<br>", $lines);
                                    })
                                    ->html()
                                    ->wrap(),
                            ])
                            ->columnSpanFull(),

                        // ---------------------------
                        // Old Values
                        // ---------------------------
                        Section::make('Old Values')
                            ->description('The previous values before this action')
                            ->schema([
                                TextEntry::make('old')
                                    ->label('Attributes')
                                    ->getStateUsing(function ($record) {
                                        $old = $record->properties['old'] ?? [];
                                        if (empty($old)) {
                                            return 'Hidden';
                                        }

                                        $lines = [];
                                        foreach ($old as $key => $value) {
                                            if ($value instanceof \DateTimeInterface) {
                                                $value = $value->format('M j, Y g:i A');
                                            } elseif (is_string($value) && strtotime($value) !== false) {
                                                $time = strtotime($value);
                                                if ($time !== false) {
                                                    $value = date('M j, Y g:i A', $time);
                                                }
                                            } elseif (is_bool($value)) {
                                                $value = $value ? 'Yes' : 'No';
                                            } elseif (is_null($value)) {
                                                $value = 'None';
                                            }
                                            $lines[] = "<strong>" . ucwords(str_replace('_', ' ', $key)) . ":</strong> " . $value;
                                        }

                                        return implode("<br>", $lines);
                                    })
                                    ->html()
                                    ->wrap(),
                            ])
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull(),
            ]);
    }
}
