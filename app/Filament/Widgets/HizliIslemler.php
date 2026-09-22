<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Admins\AdminResource;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Videos\VideoResource;
use Filament\Widgets\Widget;

class HizliIslemler extends Widget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = ['md' => 12, 'xl' => 4];

    protected string $view = 'filament.widgets.hizli-islemler';

    protected function getViewData(): array
    {
        return [
            'islemler' => [
                ['Ürün ekle', 'heroicon-o-squares-plus', ProductResource::getUrl('create')],
                ['Referans ekle', 'heroicon-o-photo', ProjectResource::getUrl('create')],
                ['Blog yazısı', 'heroicon-o-pencil-square', PostResource::getUrl('create')],
                ['CN TV videosu', 'heroicon-o-play-circle', VideoResource::getUrl('create')],
                ['Yönetici ekle', 'heroicon-o-user-plus', AdminResource::getUrl('create')],
            ],
        ];
    }
}
