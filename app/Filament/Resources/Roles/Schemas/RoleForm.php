<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Permission;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Role Name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                
                Textarea::make('description')
                    ->label('Description')
                    ->maxLength(500)
                    ->rows(3),
                
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                
                CheckboxList::make('permissions')
                    ->label('Permissions')
                    ->relationship('permissions', 'name')
                    ->options(Permission::all()->pluck('description', 'id'))
                    ->columns(2)
                    ->searchable()
                    ->bulkToggleable(),
            ]);
    }
}
