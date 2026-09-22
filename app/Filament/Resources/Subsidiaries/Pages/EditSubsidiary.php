<?php

namespace App\Filament\Resources\Subsidiaries\Pages;

use App\Filament\Resources\Subsidiaries\SubsidiaryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubsidiary extends EditRecord
{
    protected static string $resource = SubsidiaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
