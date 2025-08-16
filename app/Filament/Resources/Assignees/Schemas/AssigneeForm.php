<?php

namespace App\Filament\Resources\Assignees\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AssigneeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Assignee Information')
                    ->description('Basic information about the assignee')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., John Doe'),
                        
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('john.doe@company.com'),
                        
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+1-555-0123'),
                        
                        TextInput::make('employee_id')
                            ->label('Employee ID')
                            ->maxLength(50)
                            ->placeholder('EMP001'),
                        
                        TextInput::make('department')
                            ->label('Department')
                            ->maxLength(100)
                            ->placeholder('e.g., IT, Finance, HR'),
                        
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive assignees will not be available for new asset assignments'),
                        
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Additional information about this assignee')
                            ->columnSpan(2),
                    ]),
            ]);
    }
}
