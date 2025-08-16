<?php

namespace App\Filament\Resources\Regions\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RegionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Region Information')
                    ->description('Basic information about the geographic region')
                    ->schema([
                        TextInput::make('name')
                            ->label('Region Name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g., US East Coast, Europe, Asia Pacific'),
                    ]),
            ]);
    }
}
