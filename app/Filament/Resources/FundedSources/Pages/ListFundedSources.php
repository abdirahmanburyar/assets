<?php

namespace App\Filament\Resources\FundedSources\Pages;

use App\Filament\Resources\FundedSources\FundedSourceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFundedSources extends ListRecords
{
    protected static string $resource = FundedSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
