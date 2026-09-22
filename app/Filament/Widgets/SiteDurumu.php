<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\SiteSettings;
use App\Models\Setting;
use Filament\Widgets\Widget;

class SiteDurumu extends Widget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = ['md' => 12, 'xl' => 4];

    protected string $view = 'filament.widgets.site-durumu';

    protected function getViewData(): array
    {
        return [
            'yapim' => (bool) Setting::get('construction_enabled'),
            'pin' => filled(Setting::get('construction_pin')),
            'katalog' => (bool) config('commerce.catalog'),
            'siteUrl' => url('/'),
            'ayarlarUrl' => SiteSettings::getUrl(),
        ];
    }
}
