<?php

namespace App\Filament\Resources\SubLocations\Pages;

use App\Filament\Resources\SubLocations\SubLocationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubLocation extends EditRecord
{
    protected static string $resource = SubLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
