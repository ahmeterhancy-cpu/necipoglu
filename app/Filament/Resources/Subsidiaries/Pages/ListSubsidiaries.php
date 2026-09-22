<?php

namespace App\Filament\Resources\Subsidiaries\Pages;

use App\Filament\Resources\Subsidiaries\SubsidiaryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubsidiaries extends ListRecords
{
    protected static string $resource = SubsidiaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
