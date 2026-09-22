<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Brands\BrandResource;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\Products\ProductResource;
use App\Models\Brand;
use App\Models\ContactMessage;
use App\Models\Product;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OzetKartlari extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $okunmamis = ContactMessage::whereNull('read_at')->count();

        // Son 30 günün günlük mesaj sayısı — kartın altındaki küçük eğri.
        $gunluk = ContactMessage::query()
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->get(['created_at'])
            ->countBy(fn ($m) => $m->created_at->format('Y-m-d'));

        $seri = collect(range(29, 0))
            ->map(fn ($i) => $gunluk[now()->subDays($i)->format('Y-m-d')] ?? 0)
            ->all();

        $ayToplam = array_sum($seri);
        $onceki = ContactMessage::whereBetween('created_at', [now()->subDays(59)->startOfDay(), now()->subDays(30)->endOfDay()])->count();

        $urun = Product::count();
        $yayinda = Product::where('is_active', true)->count();

        $marka = Brand::count();
        $icerikli = Brand::all()->filter(fn (Brand $b) => filled($b->description) && count($b->galleryImages()) > 0)->count();

        return [
            Stat::make('Okunmamış mesaj', $okunmamis)
                ->description($okunmamis ? 'Yanıt bekliyor' : 'Hepsi okundu')
                ->descriptionIcon($okunmamis ? Heroicon::OutlinedEnvelope : Heroicon::OutlinedCheckCircle)
                ->color($okunmamis ? 'warning' : 'success')
                ->url(ContactMessageResource::getUrl('index')),

            Stat::make('Son 30 gün', $ayToplam.' mesaj')
                ->description($this->karsilastir($ayToplam, $onceki))
                ->descriptionIcon($ayToplam >= $onceki ? Heroicon::OutlinedArrowTrendingUp : Heroicon::OutlinedArrowTrendingDown)
                ->chart($seri)
                ->color('primary'),

            Stat::make('Yayındaki ürün', $yayinda)
                ->description($urun === $yayinda ? 'Katalogdaki tüm ürünler' : ($urun - $yayinda).' ürün taslakta')
                ->descriptionIcon(Heroicon::OutlinedSquares2x2)
                ->color('gray')
                ->url(ProductResource::getUrl('index')),

            Stat::make('İçeriği tam marka', $icerikli.' / '.$marka)
                ->description('Tanıtım yazısı ve galerisi olan')
                ->descriptionIcon(Heroicon::OutlinedSparkles)
                ->color($icerikli === $marka ? 'success' : 'gray')
                ->url(BrandResource::getUrl('index')),
        ];
    }

    private function karsilastir(int $simdi, int $once): string
    {
        if ($once === 0) {
            return $simdi ? 'Önceki 30 günde mesaj yoktu' : 'Henüz mesaj gelmedi';
        }

        $fark = (int) round(($simdi - $once) / $once * 100);

        return ($fark >= 0 ? '+' : '').$fark.'% önceki 30 güne göre';
    }
}
