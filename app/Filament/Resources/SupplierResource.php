<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Supplier;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SupplierResource\Pages;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Purchase Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Supplier Name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('company_name')
                                ->label('Company Name')
                                ->maxLength(255),
                        ]),
                        Textarea::make('address')
                            ->label('Address')
                            ->rows(3),
                    ]),

                Section::make('Contact Information')
                    ->schema([
                        Repeater::make('contact_persons')
                            ->label('Contact Persons')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('name')
                                        ->label('Contact Person Name')
                                        ->required(),
                                    TextInput::make('position')
                                        ->label('Position'),
                                ]),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['name'] ?? null),

                        Repeater::make('phone_numbers')
                            ->label('Phone Numbers')
                            ->schema([
                                TextInput::make('number')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->required(),
                            ])
                            ->defaultItems(1)
                            ->reorderable(false)
                    ->disableItemDeletion(fn(array $state): bool => count($state) <= 1)
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['number'] ?? null),

                        Repeater::make('email_addresses')
                            ->label('Email Addresses')
                            ->schema([
                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required(),
                            ])
                            ->defaultItems(1)
                            ->reorderable(false)
                            ->disableItemDeletion(fn (array $state): bool => count($state) <= 1)
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['email'] ?? null),
                    ]),

                Section::make('Business Information')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('website')
                                ->label('Website')
                                ->url(),
                            TextInput::make('tax_id')
                                ->label('Tax ID'),
                        ]),
                        TextInput::make('business_license')
                            ->label('Business License'),
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3),
                    ]),

                Section::make('Status')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'suspended' => 'Suspended',
                            ])
                            ->default('active')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Supplier Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('primary_contact')
                    ->label('Primary Contact')
                    ->searchable(),
                TextColumn::make('primary_phone')
                    ->label('Primary Phone')
                    ->searchable(),
                TextColumn::make('primary_email')
                    ->label('Primary Email')
                    ->searchable(),
                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'suspended',
                    ]),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ]),
                Filter::make('has_contacts')
                    ->label('Has Contact Persons')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('contact_persons')),
                Filter::make('has_phones')
                    ->label('Has Phone Numbers')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('phone_numbers')),
                Filter::make('has_emails')
                    ->label('Has Email Addresses')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('email_addresses')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                ActivityLogTimelineTableAction::make('Activities')
                    ->timelineIcons([
                        'created' => 'heroicon-m-check-badge',
                        'updated' => 'heroicon-m-pencil-square',
                        'deleted' => 'heroicon-m-trash',
                    ])
                    ->timelineIconColors([
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                    ])
                    ->limit(20),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
