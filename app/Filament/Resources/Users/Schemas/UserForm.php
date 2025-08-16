<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Role;
use App\Traits\HasPermissions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->minLength(8)
                    ->confirmed(),
                
                TextInput::make('password_confirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->minLength(8),
                
                Select::make('roles')
                    ->label('Roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->options(Role::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->visible(fn (): bool => HasPermissions::userCan('user.assign_role')),
                
                Toggle::make('email_verified_at')
                    ->label('Email Verified')
                    ->default(false),
            ]);
    }
}
