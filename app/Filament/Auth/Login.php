<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Panelin giriş ekranı. Filament'in hazır formu korunur; görünüm sitenin
 * antrasit kimliğine çekilir (AdminPanelProvider'daki render hook'lar +
 * resources/views/filament/auth/*).
 */
class Login extends BaseLogin
{
    public function getHeading(): string|Htmlable
    {
        return 'Yönetim paneli';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Devam etmek için giriş yapın.';
    }
}
