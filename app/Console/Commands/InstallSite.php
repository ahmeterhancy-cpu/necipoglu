<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Setting;
use Database\Seeders\BrandContentSeeder;
use Database\Seeders\BrandSeeder;
use Database\Seeders\CoreSeeder;
use Illuminate\Console\Command;

/**
 * Canlı sunucunun İLK kurulumu: şubeler, kategoriler, site ayarları ve
 * markalar. Deploy görevleri arasında her seferinde çalışır ama yalnızca
 * veritabanı BOŞKEN (hiç ayar yokken) iş yapar; sonraki deploy'larda panelde
 * yapılan hiçbir değişikliğe dokunmaz.
 *
 * `db:seed` deploy'a bilerek konmadı: CoreSeeder updateOrCreate kullanıyor,
 * her deploy'da paneldeki metinleri kurulum metinleriyle ezerdi.
 *
 * Demo içerik (DemoContentSeeder: örnek ürünler ve referanslar) canlıya
 * GİRMEZ. Site ilk açıldığında yapım aşaması sayfası açık gelir.
 */
class InstallSite extends Command
{
    protected $signature = 'site:kur {--zorla : Ayar olsa bile kurulum tohumlarını yeniden çalıştır}';

    protected $description = 'Boş veritabanına şube, kategori, ayar ve markaları yükler; yapım aşamasını açar';

    public function handle(): int
    {
        if (Setting::query()->exists() && ! $this->option('zorla')) {
            $this->info('Veritabanı dolu, kurulum atlandı.');

            return self::SUCCESS;
        }

        $this->call('db:seed', ['--class' => CoreSeeder::class, '--force' => true]);
        $this->call('db:seed', ['--class' => BrandSeeder::class, '--force' => true]);
        $this->call('db:seed', ['--class' => BrandContentSeeder::class, '--force' => true]);

        // Yer tutucu görseller: deploy bunları storage/media'ya koyuyor
        // (deploy/ilk-medya). Gerçek fotoğraflar panelden yüklenince yerlerini
        // alır. Örnek ürün/referans görselleri bilerek bağlanmıyor.
        Setting::put('hero_image', 'media/hero.jpg', 'home');
        Setting::put('scene_image', 'media/scene.jpg', 'home');

        foreach (Category::all() as $category) {
            $category->update([
                'thumbnail' => "media/cat-{$category->slug}.jpg",
                'cover' => "media/big-{$category->slug}.jpg",
            ]);
        }

        // Yayına hazır olana kadar ziyaretçi siteyi değil yapım aşaması
        // sayfasını görür. PIN .env'de yoksa yalnızca panele giriş yapmış
        // yönetici siteyi görebilir; PIN panelden sonra verilebilir.
        Setting::put('construction_enabled', true);

        if (filled($pin = config('site.construction_pin'))) {
            Setting::put('construction_pin', (string) $pin);
        }

        $this->info('Kurulum tamam. Yapım aşaması sayfası AÇIK.');

        return self::SUCCESS;
    }
}
