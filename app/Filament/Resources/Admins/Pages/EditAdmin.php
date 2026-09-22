<?php

namespace App\Filament\Resources\Admins\Pages;

use App\Filament\Resources\Admins\AdminResource;
use Filament\Resources\Pages\EditRecord;

class EditAdmin extends EditRecord
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            AdminResource::deleteAction(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
