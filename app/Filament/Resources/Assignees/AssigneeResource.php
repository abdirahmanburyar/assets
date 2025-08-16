<?php

namespace App\Filament\Resources\Assignees;

use App\Filament\Resources\Assignees\Pages\CreateAssignee;
use App\Filament\Resources\Assignees\Pages\EditAssignee;
use App\Filament\Resources\Assignees\Pages\ListAssignees;
use App\Filament\Resources\Assignees\Schemas\AssigneeForm;
use App\Filament\Resources\Assignees\Tables\AssigneesTable;
use App\Models\Assignee;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AssigneeResource extends Resource
{
    protected static ?string $model = Assignee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    // protected static $navigationGroup = 'Asset Management';

    protected static ?int $navigationSort = 7;

    public static function getNavigationLabel(): string
    {
        return 'Assignees';
    }

    public static function form(Schema $schema): Schema
    {
        return AssigneeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssigneesTable::configure($table);
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
            'index' => ListAssignees::route('/'),
            'create' => CreateAssignee::route('/create'),
            'edit' => EditAssignee::route('/{record}/edit'),
        ];
    }
}
