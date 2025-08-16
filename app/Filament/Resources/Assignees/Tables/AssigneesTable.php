<?php

namespace App\Filament\Resources\Assignees\Tables;

use App\Models\Assignee;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AssigneesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                TextColumn::make('department')
                    ->label('Department')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                
                TextColumn::make('employee_id')
                    ->label('Employee ID')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                
                TextColumn::make('active_assets_count')
                    ->label('Active Assets')
                    ->getStateUsing(fn (Assignee $record): int => $record->active_assets_count)
                    ->sortable(),
                
                TextColumn::make('total_assets_value')
                    ->label('Total Assets Value')
                    ->getStateUsing(fn (Assignee $record): string => '$' . number_format($record->total_assets_value, 2))
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
                SelectFilter::make('department')
                    ->label('Department')
                    ->options(Assignee::distinct()->pluck('department', 'department')->filter())
                    ->searchable()
                    ->multiple(),
                
                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All Assignees')
                    ->trueLabel('Active Assignees')
                    ->falseLabel('Inactive Assignees'),
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
