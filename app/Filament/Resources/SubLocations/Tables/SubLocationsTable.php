<?php

namespace App\Filament\Resources\SubLocations\Tables;

use App\Models\SubLocation;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubLocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Sub-Location Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('location.name')
                    ->label('Location')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                
                TextColumn::make('location.region.name')
                    ->label('Region')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                
                TextColumn::make('full_name')
                    ->label('Full Path')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                
                TextColumn::make('active_assets_count')
                    ->label('Active Assets')
                    ->getStateUsing(fn (SubLocation $record): int => $record->active_assets_count)
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('location_id')
                    ->label('Location')
                    ->options(\App\Models\Location::pluck('name', 'id'))
                    ->searchable()
                    ->multiple(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name')
            ->searchable();
    }
}
