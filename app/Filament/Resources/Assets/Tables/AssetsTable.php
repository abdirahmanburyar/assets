<?php

namespace App\Filament\Resources\Assets\Tables;

use App\Models\Asset;
use App\Models\Assignee;
use App\Models\Category;
use App\Models\Location;
use App\Models\Region;
use App\Models\SubLocation;
use App\Models\FundedSource;
use App\Traits\HasPermissions;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset_tag')
                    ->label('Asset Tag')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (Asset $record): string => $record->category?->color ?? 'gray'),
                
                TextColumn::make('item_description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable()
                    ->wrap(),
                
                TextColumn::make('full_location_path')
                    ->label('Location')
                    ->searchable()
                    ->limit(40)
                    ->wrap(),
                
                TextColumn::make('assignee.name')
                    ->label('Assigned To')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Unassigned'),
                
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'in_use',
                        'info' => 'maintenance',
                        'gray' => 'retired',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),
                
                TextColumn::make('original_value')
                    ->label('Original Value')
                    ->money('USD')
                    ->sortable(),
                
                TextColumn::make('current_value')
                    ->label('Current Value')
                    ->money('USD')
                    ->sortable()
                    ->color(fn (Asset $record): string => 
                        $record->current_value < $record->original_value * 0.5 ? 'danger' : 'success'
                    ),
                
                TextColumn::make('acquisition_date')
                    ->label('Acquired')
                    ->date()
                    ->sortable(),
                
                TextColumn::make('warranty_expiry')
                    ->label('Warranty')
                    ->date()
                    ->sortable()
                    ->color(fn (Asset $record): string => 
                        $record->is_warranty_expiring_soon ? 'warning' : 'success'
                    ),
                
                TextColumn::make('next_maintenance_date')
                    ->label('Next Maintenance')
                    ->date()
                    ->sortable()
                    ->color(fn (Asset $record): string => 
                        $record->is_maintenance_due_soon ? 'warning' : 'success'
                    ),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending Approval',
                        'in_use' => 'In Use',
                        'maintenance' => 'Under Maintenance',
                        'retired' => 'Retired',
                        'rejected' => 'Rejected',
                    ])
                    ->multiple(),
                
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(Category::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->multiple(),
                
                SelectFilter::make('region_id')
                    ->label('Region')
                    ->options(Region::pluck('name', 'id'))
                    ->searchable()
                    ->multiple()
                    ->relationship('location.region', 'name'),
                
                SelectFilter::make('location_id')
                    ->label('Location')
                    ->options(Location::pluck('name', 'id'))
                    ->searchable()
                    ->multiple(),
                
                SelectFilter::make('assigned_to_id')
                    ->label('Assigned To')
                    ->options(Assignee::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->multiple(),
                
                Filter::make('warranty_expiring_soon')
                    ->label('Warranty Expiring Soon')
                    ->query(fn (Builder $query): Builder => $query->warrantyExpiringSoon(30))
                    ->toggle(),
                
                Filter::make('maintenance_due_soon')
                    ->label('Maintenance Due Soon')
                    ->query(fn (Builder $query): Builder => $query->maintenanceDueSoon(7))
                    ->toggle(),
                
                Filter::make('high_value')
                    ->label('High Value Assets')
                    ->query(fn (Builder $query): Builder => $query->where('original_value', '>=', 10000))
                    ->toggle(),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('view')
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->url(fn (Asset $record): string => route('filament.admin.resources.assets.view', $record))
                        ->openUrlInNewTab(),
                    
                    Action::make('edit')
                        ->label('Edit')
                        ->icon('heroicon-o-pencil')
                        ->visible(fn (): bool => HasPermissions::userCan('asset.edit')),
                    
                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Asset $record): bool => 
                            $record->status === 'pending' && HasPermissions::userCan('asset.approve')
                        )
                        ->action(function (Asset $record): void {
                            $record->update([
                                'status' => 'in_use',
                                'approved_by' => auth()->id(),
                                'approved_at' => now(),
                            ]);
                        }),
                    
                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (Asset $record): bool => 
                            $record->status === 'pending' && HasPermissions::userCan('asset.reject')
                        )
                        ->action(function (Asset $record): void {
                            $record->update(['status' => 'rejected']);
                        }),
                    
                    Action::make('mark_maintenance')
                        ->label('Mark for Maintenance')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->color('warning')
                        ->visible(fn (Asset $record): bool => 
                            in_array($record->status, ['in_use', 'pending']) && HasPermissions::userCan('asset.edit')
                        )
                        ->action(function (Asset $record): void {
                            $record->update(['status' => 'maintenance']);
                        }),
                    
                    Action::make('complete_maintenance')
                        ->label('Complete Maintenance')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->visible(fn (Asset $record): bool => 
                            $record->status === 'maintenance' && HasPermissions::userCan('asset.edit')
                        )
                        ->action(function (Asset $record): void {
                            $record->update(['status' => 'in_use']);
                        }),
                    
                    Action::make('retire')
                        ->label('Retire')
                        ->icon('heroicon-o-archive-box')
                        ->color('gray')
                        ->visible(fn (Asset $record): bool => 
                            in_array($record->status, ['in_use', 'maintenance']) && HasPermissions::userCan('asset.retire')
                        )
                        ->action(function (Asset $record): void {
                            $record->update(['status' => 'retired']);
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    Action::make('bulk_approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (): bool => HasPermissions::userCan('asset.approve'))
                        ->action(function () {
                            $records = $this->getSelectedTableRecords();
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->update([
                                        'status' => 'in_use',
                                        'approved_by' => auth()->id(),
                                        'approved_at' => now(),
                                    ]);
                                }
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                    
                    Action::make('bulk_reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn (): bool => HasPermissions::userCan('asset.reject'))
                        ->action(function () {
                            $records = $this->getSelectedTableRecords();
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->update(['status' => 'rejected']);
                                }
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                    
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => HasPermissions::userCan('asset.delete')),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->searchable()
            ->paginated([10, 25, 50, 100])
            ->striped();
    }
}
