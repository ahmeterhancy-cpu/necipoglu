<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Project;
use App\Models\Subsidiary;
use App\Models\Video;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            'categories' => Category::query()
                ->active()
                ->whereNull('parent_id')
                ->where('show_on_home', true)
                ->ordered()
                ->get(),

            'featuredProducts' => Product::query()
                ->active()
                ->featured()
                ->with(['category', 'brand'])
                ->ordered()
                ->take(8)
                ->get(),

            'projects' => Project::query()->active()->featured()->ordered()->take(4)->get(),
            // Ana sayfada tam liste değil seçilenler: 30 marka duvarı
            // beş sıra tutuyordu. Tamamı Markalar sayfasında.
            'brands' => Brand::query()
                ->active()
                ->when(
                    Brand::query()->active()->featured()->exists(),
                    fn ($q) => $q->featured(),
                    fn ($q) => $q->limit(12),
                )
                ->ordered()
                ->get(),
            'video' => Video::query()->active()->featured()->ordered()->first(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about', [
            'subsidiaries' => Subsidiary::query()->active()->ordered()->get(),
            'categories' => Category::query()->active()->ordered()->get(),
            'brands' => Brand::query()->active()->ordered()->get(),
            'branches' => Branch::query()->active()->ordered()->get(),
        ]);
    }

    /**
     * Yapı Market — grubun ikinci perakende kolu. İştiraklerden ayrı
     * duruyor; kendi sayfası ve ana sayfada kendi şeridi var.
     *
     * İçerik ayarlardan gelir, görseller public/market/gallery/ altından.
     */
    public function market(): View
    {
        // Şube listesi BİLEREK gönderilmiyor: alt bilgi zaten üç şubeyi
        // adres ve telefonuyla basıyor, sayfada iki kez görünüyordu.
        return view('pages.market', [
            'gallery' => $this->marketGallery(),
        ]);
    }

    /**
     * Yapı Market görselleri — marka galerisiyle aynı mantık: klasöre
     * atılan dosyalar dosya adına göre doğal sırada okunur.
     *
     * @return list<string>
     */
    protected function marketGallery(): array
    {
        $dizin = public_path('market/gallery');

        if (! is_dir($dizin)) {
            return [];
        }

        $dosyalar = glob($dizin.'/*.{jpg,jpeg,png,webp,avif,JPG,JPEG,PNG,WEBP,AVIF}', GLOB_BRACE) ?: [];
        natcasesort($dosyalar);

        return array_values(array_map(
            fn (string $f) => asset('market/gallery/'.basename($f)).'?v='.filemtime($f),
            $dosyalar,
        ));
    }

    public function subsidiaries(): View
    {
        return view('pages.subsidiaries', [
            'subsidiaries' => Subsidiary::query()->active()->ordered()->get(),
        ]);
    }
}
