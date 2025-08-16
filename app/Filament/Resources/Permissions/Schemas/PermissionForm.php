<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Permission Name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Use dot notation (e.g., asset.create, user.edit)'),
                
                Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->maxLength(500)
                    ->rows(3),
                
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}
