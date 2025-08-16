<?php

namespace App\Filament\Resources\SubLocations\Pages;

use App\Filament\Resources\SubLocations\SubLocationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubLocations extends ListRecords
{
    protected static string $resource = SubLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
