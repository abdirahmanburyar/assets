<?php

namespace App\Filament\Resources\FundedSources\Pages;

use App\Filament\Resources\FundedSources\FundedSourceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFundedSource extends EditRecord
{
    protected static string $resource = FundedSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
