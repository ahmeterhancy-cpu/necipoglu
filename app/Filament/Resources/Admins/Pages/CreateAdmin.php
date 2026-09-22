<?php

namespace App\Filament\Resources\Admins\Pages;

use App\Filament\Resources\Admins\AdminResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;

    /** is_admin toplu atamaya kapalı (Fillable listesinde yok) — bilerek. */
    protected function handleRecordCreation(array $data): Model
    {
        $user = new User($data);
        $user->forceFill(['is_admin' => true, 'email_verified_at' => now()])->save();

        return $user;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
