<?php

namespace App\Filament\Resources\FundedSources\Pages;

use App\Filament\Resources\FundedSources\FundedSourceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFundedSource extends CreateRecord
{
    protected static string $resource = FundedSourceResource::class;
}
