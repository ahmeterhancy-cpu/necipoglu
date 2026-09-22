<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Panelin açılış ekranı. Varsayılan "Hoş geldin + Oturumu kapat" kartının
 * yerine günlük işi özetler: gelen talepler, site durumu, yayın hazırlığı.
 * Bileşenler app/Filament/Widgets altında, 12 sütunluk ızgarada dizilir.
 */
class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Panel';

    public function getColumns(): int|array
    {
        return ['md' => 12];
    }

    public function getHeading(): string|Htmlable
    {
        $saat = (int) now('Asia/Famagusta')->format('G');

        $selam = match (true) {
            $saat < 5 => 'İyi geceler',
            $saat < 12 => 'Günaydın',
            $saat < 18 => 'İyi günler',
            default => 'İyi akşamlar',
        };

        return $selam.', '.str(auth()->user()?->name ?? 'Yönetici')->before(' ');
    }

    public function getSubheading(): ?string
    {
        return now('Asia/Famagusta')->locale('tr')->translatedFormat('j F Y, l');
    }
}
