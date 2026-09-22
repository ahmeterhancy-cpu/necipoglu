<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    use HasTranslations, Publishable;

    protected array $translatable = ['description'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'highlights' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'position' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Logo adresi. Üç kaynak sırayla denenir:
     *   1. Panelden yüklenen dosya (logo sütunu, public disk)
     *   2. public/brand/logos/<slug>.(svg|png|webp|jpg) — panel hazır
     *      olmadan logo eklemek için; dosyayı klasöre koymak yeterli
     *   3. Yoksa null; görünüm marka adını tipografiyle gösterir
     */
    public function logoUrl(): ?string
    {
        if ($this->logo) {
            return Storage::url($this->logo);
        }

        foreach (['svg', 'png', 'webp', 'jpg'] as $extension) {
            $relative = "brand/logos/{$this->slug}.{$extension}";

            if (is_file(public_path($relative))) {
                // Dosya değişince tarayıcı önbelleği yenilensin.
                return asset($relative).'?v='.filemtime(public_path($relative));
            }
        }

        return null;
    }

    /**
     * Markanın ürün grupları — o anki dilde, kısa etiketler listesi.
     * Dil karşılığı yoksa varsayılan dile düşer, o da yoksa boş döner.
     *
     * @return list<string>
     */
    public function highlightList(): array
    {
        $liste = $this->highlights ?? [];

        return $liste[app()->getLocale()]
            ?? $liste[config('site.default_locale')]
            ?? [];
    }

    /**
     * Marka sayfasındaki görseller. Logodaki mantığın aynısı — iki kaynak:
     *   1. Panelden yüklenenler (images sütunu, public disk)
     *   2. public/brand/gallery/<slug>/ klasöründeki dosyalar; panele
     *      girmeden görsel eklemek için, dosya adına göre sıralanır
     *
     * Panelden bir tane bile yüklenmişse klasöre BAKILMAZ — iki kaynağı
     * karıştırmak sıralamayı öngörülemez hale getirir.
     *
     * @return list<string>
     */
    public function galleryImages(): array
    {
        if (filled($this->images)) {
            return array_map(fn (string $path) => Storage::url($path), $this->images);
        }

        $directory = public_path("brand/gallery/{$this->slug}");

        if (! is_dir($directory)) {
            return [];
        }

        $files = glob($directory.'/*.{jpg,jpeg,png,webp,avif,JPG,JPEG,PNG,WEBP,AVIF}', GLOB_BRACE) ?: [];

        // Dosya adına göre doğal sıra: 2.jpg, 10.jpg'den önce gelsin.
        natcasesort($files);

        return array_values(array_map(
            fn (string $file) => asset("brand/gallery/{$this->slug}/".basename($file)).'?v='.filemtime($file),
            $files,
        ));
    }

    /*
     * linkUrl() / linksOut() kaldırıldı: marka artık doğrudan dış siteye
     * değil kendi sayfasına gidiyor, dış bağlantı orada duruyor.
     */
}
