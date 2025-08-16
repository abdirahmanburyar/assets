<?php

namespace App\Filament\Resources\FundedSources\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FundedSourceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Funded Source Information')
                    ->description('Basic information about the funding source')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Source Name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g., Federal Grant Program, State Department'),
                        
                        TextInput::make('code')
                            ->label('Source Code')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g., FED-GRANT, STATE-TECH')
                            ->helperText('Short unique identifier for the funding source'),
                        
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive funding sources will not be available for new assets')
                            ->columnSpan(2),
                    ]),
            ]);
    }
}
