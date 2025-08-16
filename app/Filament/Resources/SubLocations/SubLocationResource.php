<?php

namespace App\Filament\Resources\SubLocations;

use App\Filament\Resources\SubLocations\Pages\CreateSubLocation;
use App\Filament\Resources\SubLocations\Pages\EditSubLocation;
use App\Filament\Resources\SubLocations\Pages\ListSubLocations;
use App\Filament\Resources\SubLocations\Schemas\SubLocationForm;
use App\Filament\Resources\SubLocations\Tables\SubLocationsTable;
use App\Models\SubLocation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SubLocationResource extends Resource
{
    protected static ?string $model = SubLocation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    // protected static $navigationGroup = 'Asset Management';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return 'Sub-Locations';
    }

    public static function form(Schema $schema): Schema
    {
        return SubLocationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubLocationsTable::configure($table);
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
            'index' => ListSubLocations::route('/'),
            'create' => CreateSubLocation::route('/create'),
            'edit' => EditSubLocation::route('/{record}/edit'),
        ];
    }
}
