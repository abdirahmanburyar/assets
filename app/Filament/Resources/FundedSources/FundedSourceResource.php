<?php

namespace App\Filament\Resources\FundedSources;

use App\Filament\Resources\FundedSources\Pages\CreateFundedSource;
use App\Filament\Resources\FundedSources\Pages\EditFundedSource;
use App\Filament\Resources\FundedSources\Pages\ListFundedSources;
use App\Filament\Resources\FundedSources\Schemas\FundedSourceForm;
use App\Filament\Resources\FundedSources\Tables\FundedSourcesTable;
use App\Models\FundedSource;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FundedSourceResource extends Resource
{
    protected static ?string $model = FundedSource::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    // protected static $navigationGroup = 'Asset Management';

    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return 'Funded Sources';
    }

    public static function form(Schema $schema): Schema
    {
        return FundedSourceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FundedSourcesTable::configure($table);
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
            'index' => ListFundedSources::route('/'),
            'create' => CreateFundedSource::route('/create'),
            'edit' => EditFundedSource::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'success' : 'primary';
    }
}
