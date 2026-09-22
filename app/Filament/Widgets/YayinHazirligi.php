<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\Brands\BrandResource;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Videos\VideoResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Video;
use Filament\Widgets\Widget;

/**
 * Yayına açmadan önce tamamlanması gerekenler. Liste veriden hesaplanır;
 * madde tamamlandıkça kendiliğinden işaretlenir. Kurulumla gelen yer tutucu
 * görseller media/<ad>.jpg adıyla durur — panelden yüklenen gerçek fotoğraf
 * başka bir ad alır, madde o anda tamamlanmış sayılır.
 */
class YayinHazirligi extends Widget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = ['md' => 12, 'xl' => 8];

    protected string $view = 'filament.widgets.yayin-hazirligi';

    protected function getViewData(): array
    {
        $markalar = Brand::all();
        $yaziYok = $markalar->filter(fn (Brand $b) => blank($b->description))->count();
        $galeriYok = $markalar->filter(fn (Brand $b) => count($b->galleryImages()) === 0)->count();

        $yerTutucuGorsel = collect(['hero_image', 'scene_image', 'market_image'])
            ->filter(fn ($k) => in_array(Setting::get($k), ['media/hero.jpg', 'media/scene.jpg', 'media/market.jpg'], true))
            ->count();
        $yerTutucuKategori = Category::where('thumbnail', 'like', 'media/cat-%')->count();

        $sosyal = collect(['social_instagram', 'social_facebook', 'social_youtube', 'social_linkedin'])
            ->filter(fn ($k) => filled(Setting::get($k)))
            ->count();

        $market = collect(['market_address', 'market_phone', 'market_hours'])
            ->filter(fn ($k) => filled(Setting::get($k)))
            ->count();

        $maddeler = [
            [
                'baslik' => 'Ana sayfa ve Yapı Market fotoğrafları',
                'detay' => $yerTutucuGorsel ? $yerTutucuGorsel.' görsel hâlâ örnek fotoğraf' : 'Gerçek fotoğraflar yüklendi',
                'tamam' => $yerTutucuGorsel === 0,
                'url' => SiteSettings::getUrl(),
            ],
            [
                'baslik' => 'Kategori görselleri',
                'detay' => $yerTutucuKategori ? $yerTutucuKategori.' kategoride örnek görsel var' : 'Hepsi gerçek görsel',
                'tamam' => $yerTutucuKategori === 0,
                'url' => CategoryResource::getUrl('index'),
            ],
            [
                'baslik' => 'Katalog ürünleri',
                'detay' => ($n = Product::where('is_active', true)->count()) ? $n.' ürün yayında' : 'Katalogda henüz ürün yok',
                'tamam' => $n > 0,
                'url' => ProductResource::getUrl('index'),
            ],
            [
                'baslik' => 'Referans projeler',
                'detay' => ($n = Project::where('is_active', true)->count()) ? $n.' proje yayında' : 'Henüz referans eklenmedi',
                'tamam' => $n > 0,
                'url' => ProjectResource::getUrl('index'),
            ],
            [
                'baslik' => 'Marka tanıtım yazıları',
                'detay' => $yaziYok ? $yaziYok.' markanın yazısı eksik' : 'Tüm markalar tamam',
                'tamam' => $yaziYok === 0,
                'url' => BrandResource::getUrl('index'),
            ],
            [
                'baslik' => 'Marka galerileri',
                'detay' => $galeriYok ? $galeriYok.' markanın galerisi boş' : 'Tüm markalar tamam',
                'tamam' => $galeriYok === 0,
                'url' => BrandResource::getUrl('index'),
            ],
            [
                'baslik' => 'CN Yapı Market bilgileri',
                'detay' => $market === 3 ? 'Adres, telefon ve saatler girildi' : 'Adres, telefon veya çalışma saati eksik',
                'tamam' => $market === 3,
                'url' => SiteSettings::getUrl(),
            ],
            [
                'baslik' => 'Sosyal medya hesapları',
                'detay' => $sosyal ? $sosyal.' hesap bağlı' : 'Hiç hesap girilmedi',
                'tamam' => $sosyal > 0,
                'url' => SiteSettings::getUrl(),
            ],
            [
                'baslik' => 'CN TV ve Blog',
                'detay' => (Video::count() + Post::count()) ? Video::count().' video, '.Post::count().' yazı' : 'İkisi de boş — menüde boş sayfa görünür',
                'tamam' => Video::count() > 0 && Post::count() > 0,
                'url' => Video::count() ? PostResource::getUrl('index') : VideoResource::getUrl('index'),
            ],
        ];

        $tamam = collect($maddeler)->where('tamam', true)->count();

        return [
            'maddeler' => collect($maddeler)->sortBy('tamam')->values()->all(),
            'tamam' => $tamam,
            'toplam' => count($maddeler),
            'yuzde' => (int) round($tamam / count($maddeler) * 100),
        ];
    }
}
