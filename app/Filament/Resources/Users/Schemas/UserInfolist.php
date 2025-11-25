<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;

class UserInfolist
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
                        Section::make('Account Information')
                            ->schema([
                                TextEntry::make('first_name')
                                    ->formatStateUsing(fn($state, $record) => $record->first_name . ' ' . $record->middle_name . ' ' . $record->last_name . ' ' . $record->suffix),
                                TextEntry::make('email')
                                    ->label('Email Address'),
                                IconEntry::make('email_verified_at')
                                    ->label('Email Verified')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle'),
                                TextEntry::make('number')
                                    ->placeholder("No number set")
                                    ->badge(true),
                                TextEntry::make('association')
                                    ->placeholder('No association added')
                            ]),

                        Section::make('Timestamps')
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Member Since')
                                    ->dateTime('M j, Y g:i A')
                                    ->placeholder('Not available'),
                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime('M j, Y g:i A')
                                    ->placeholder('Not available'),
                            ]),

                        // Section::make('Activity')
                        //     ->description('User activity and statistics')
                        //     ->schema([
                        //         // Add any additional user statistics or activity here
                        //         // For example:
                        //         // TextEntry::make('last_login_at')
                        //         //     ->label('Last Login')
                        //         //     ->dateTime('M j, Y g:i A')
                        //         //     ->placeholder('Never')
                        //     ])
                        //     ->columnSpanFull()
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
