<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Hash;

class UserForm
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
                            ->description('Basic user account details')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                            ]),

                        Section::make('Security')
                            ->description('Authentication settings')
                            ->schema([
                                Select::make('roles')
                                    ->relationship('roles', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),
                                TextInput::make('password')
                                    ->password()
                                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                                    ->dehydrated(fn($state) => filled($state))
                                    ->required(fn(string $context): bool => $context === 'create')
                                    ->minLength(8)
                                    ->same('password_confirmation')
                                    ->autocomplete('new-password'),
                                TextInput::make('password_confirmation')
                                    ->password()
                                    ->label('Confirm Password')
                                    ->requiredWith('password')
                                    ->dehydrated(false)
                                    ->autocomplete('new-password'),
                            ]),

                        Section::make('Status')
                            ->description('Account status and verification')
                            ->schema([
                                Toggle::make('email_verified_at')
                                    ->label('Email Verified')
                                    ->dehydrateStateUsing(fn($state) => $state ? now() : null)
                                    ->dehydrated(fn($state) => filled($state))
                                    ->hiddenOn('create'),
                            ])
                            ->columnSpanFull()
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
