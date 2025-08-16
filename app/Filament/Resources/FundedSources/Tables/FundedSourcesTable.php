<?php

namespace App\Filament\Resources\FundedSources\Tables;

use App\Models\FundedSource;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FundedSourcesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Source Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('code')
                    ->label('Source Code')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->copyable(),
                
                TextColumn::make('assets_count')
                    ->label('Total Assets')
                    ->counts('assets')
                    ->sortable(),
                
                TextColumn::make('active_assets_count')
                    ->label('Active Assets')
                    ->getStateUsing(fn (FundedSource $record): int => $record->active_assets_count)
                    ->sortable(),
                
                TextColumn::make('total_assets_value')
                    ->label('Total Value')
                    ->getStateUsing(fn (FundedSource $record): string => '$' . number_format($record->total_assets_value, 2))
                    ->sortable(),
                
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All Funding Sources')
                    ->trueLabel('Active Sources')
                    ->falseLabel('Inactive Sources'),
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
