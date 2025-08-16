<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Models\Assignee;
use App\Models\Category;
use App\Models\Location;
use App\Models\Region;
use App\Models\SubLocation;
use App\Models\FundedSource;
use App\Traits\HasPermissions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->description('Essential asset details and identification')
                    ->schema([
                        TextInput::make('asset_tag')
                            ->label('Asset Tag')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('e.g., AST-2024-001'),
                        
                        TextInput::make('serial_number')
                            ->label('Serial Number')
                            ->maxLength(100)
                            ->placeholder('Manufacturer serial number'),
                        
                        Select::make('category_id')
                            ->label('Category')
                            ->options(Category::where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Category Name')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('description')
                                    ->label('Description')
                                    ->rows(3)
                                    ->maxLength(500),
                                TextInput::make('color')
                                    ->label('Color')
                                    ->default('#3B82F6')
                                    ->maxLength(7),
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                if (!HasPermissions::userCan('category.create')) {
                                    throw new \Exception('You do not have permission to create categories.');
                                }
                                $category = Category::create($data);
                                return $category->id;
                            })
                            ->createOptionAction(fn (Action $action) => $action->label('Add New Category')),
                        
                        TextInput::make('item_description')
                            ->label('Item Description')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Dell Latitude Laptop, Office Chair, etc.'),
                    ]),
                
                Section::make('Location & Assignment')
                    ->description('Where the asset is located and who it\'s assigned to')
                    ->schema([
                        Select::make('region_id')
                            ->label('Region')
                            ->options(Region::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Region Name')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                if (!HasPermissions::userCan('location.create')) {
                                    throw new \Exception('You do not have permission to create regions.');
                                }
                                $region = Region::create($data);
                                return $region->id;
                            })
                            ->createOptionAction(fn (Action $action) => $action->label('Add New Region'))
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('location_id', null)),
                        
                        Select::make('location_id')
                            ->label('Location')
                            ->options(function (Get $get) {
                                $regionId = $get('region_id');
                                if (!$regionId) return [];
                                return Location::where('region_id', $regionId)
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Location Name')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('region_id')
                                    ->label('Region')
                                    ->options(Region::pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Select the region for this new location'),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                if (!HasPermissions::userCan('location.create')) {
                                    throw new \Exception('You do not have permission to create locations.');
                                }
                                $location = Location::create($data);
                                return $location->id;
                            })
                            ->createOptionAction(fn (Action $action) => $action->label('Add New Location'))
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('sub_location_id', null)),
                        
                        Select::make('sub_location_id')
                            ->label('Sub-Location')
                            ->options(function (Get $get) {
                                $locationId = $get('location_id');
                                if (!$locationId) return [];
                                return SubLocation::where('location_id', $locationId)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Sub-Location Name')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('location_id')
                                    ->label('Location')
                                    ->options(function (Get $get) {
                                        $regionId = $get('region_id');
                                        if (!$regionId) return [];
                                        return Location::where('region_id', $regionId)
                                            ->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Select the location where this sub-location will be created'),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                if (!HasPermissions::userCan('location.create')) {
                                    throw new \Exception('You do not have permission to create sub-locations.');
                                }
                                $subLocation = SubLocation::create($data);
                                return $subLocation->id;
                            })
                            ->createOptionAction(fn (Action $action) => $action->label('Add New Sub-Location')),
                        
                        Select::make('assigned_to_id')
                            ->label('Assigned To')
                            ->options(Assignee::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->maxLength(20),
                                TextInput::make('employee_id')
                                    ->label('Employee ID')
                                    ->maxLength(50),
                                TextInput::make('department')
                                    ->label('Department')
                                    ->maxLength(100),
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
                                Textarea::make('notes')
                                    ->label('Notes')
                                    ->rows(3)
                                    ->maxLength(500),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                if (!HasPermissions::userCan('user.create')) {
                                    throw new \Exception('You do not have permission to create users.');
                                }
                                $assignee = Assignee::create($data);
                                return $assignee->id;
                            })
                            ->createOptionAction(fn (Action $action) => $action->label('Add New Assignee')),
                    ]),
                
                Section::make('Financial Information')
                    ->description('Asset value and depreciation details')
                    ->schema([
                        TextInput::make('original_value')
                            ->label('Original Value')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->step(0.01),
                        
                        TextInput::make('depreciation_rate')
                            ->label('Depreciation Rate (%/year)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->default(0)
                            ->suffix('%'),
                        
                        DatePicker::make('depreciation_start_date')
                            ->label('Depreciation Start Date')
                            ->required()
                            ->default(now()),
                        
                        TextInput::make('current_value')
                            ->label('Current Value')
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->step(0.01)
                            ->disabled()
                            ->helperText('Automatically calculated based on original value and depreciation')
                            ->dehydrated(),
                    ]),
                
                Section::make('Acquisition & Warranty')
                    ->description('Purchase details and warranty information')
                    ->schema([
                        DatePicker::make('acquisition_date')
                            ->label('Acquisition Date')
                            ->required()
                            ->default(now()),
                        
                        DatePicker::make('warranty_expiry')
                            ->label('Warranty Expiry')
                            ->minDate(now()),
                        
                        Select::make('funded_source_id')
                            ->label('Funded Source')
                            ->options(FundedSource::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Funded Source Name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('code')
                                    ->label('Code')
                                    ->required()
                                    ->maxLength(50)
                                    ->unique(ignoreRecord: true),
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                if (!HasPermissions::userCan('category.create')) {
                                    throw new \Exception('You do not have permission to create funded sources.');
                                }
                                $fundedSource = FundedSource::create($data);
                                return $fundedSource->id;
                            })
                            ->createOptionAction(fn (Action $action) => $action->label('Add New Funded Source')),
                    ]),
                
                Section::make('Maintenance & Status')
                    ->description('Maintenance schedule and current status')
                    ->schema([
                        TextInput::make('maintenance_interval_days')
                            ->label('Maintenance Interval (days)')
                            ->numeric()
                            ->minValue(0)
                            ->step(1)
                            ->default(0)
                            ->helperText('0 = No automatic maintenance'),
                        
                        DatePicker::make('next_maintenance_date')
                            ->label('Next Maintenance Date')
                            ->disabled()
                            ->helperText('Calculated automatically based on interval'),
                        
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending Approval',
                                'in_use' => 'In Use',
                                'maintenance' => 'Under Maintenance',
                                'retired' => 'Retired',
                                'rejected' => 'Rejected',
                            ])
                            ->required()
                            ->default('pending')
                            ->searchable(),
                        
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Additional information about this asset'),
                    ]),
            ]);
    }
}
