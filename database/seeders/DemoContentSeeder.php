<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Project;
use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * GEÇİCİ demo içerik ve yer tutucu görseller.
 *
 * Görseller Unsplash'ten indirilmiştir (storage/app/public/media) ve
 * Cahit Necipoğlu'nun gerçek ürünleri DEĞİLDİR. Ürün adları, fiyatlar ve
 * referans projeler tasarımın değerlendirilebilmesi için uydurulmuştur.
 *
 * Gerçek içerik girildiğinde bu seeder çalıştırılmamalıdır:
 *     php artisan db:seed --class=CoreSeeder
 */
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->media();
        $this->brands();
        $this->products();
        $this->projects();
    }

    /** Ayarlara ve kategorilere yer tutucu görselleri bağlar. */
    protected function media(): void
    {
        Setting::put('hero_image', 'media/hero.jpg', 'home');
        Setting::put('scene_image', 'media/scene.jpg', 'home');

        foreach (Category::all() as $category) {
            $category->update([
                'thumbnail' => "media/cat-{$category->slug}.jpg",
                'cover' => "media/big-{$category->slug}.jpg",
            ]);
        }
    }

    protected function brands(): void
    {
        // DİKKAT: bu markalar yer tutucudur. Gerçekten temsil edilen
        // üreticilerle değiştirilmeden canlıya çıkılmamalı.
        $brands = [
            ['slug' => 'seranit', 'name' => 'Seranit', 'country' => 'Türkiye', 'website' => 'https://www.seranit.com'],
            ['slug' => 'vitra', 'name' => 'VitrA', 'country' => 'Türkiye', 'website' => 'https://www.vitra.com.tr'],
            ['slug' => 'kale', 'name' => 'Kale', 'country' => 'Türkiye', 'website' => 'https://www.kale.com.tr'],
            ['slug' => 'ege-seramik', 'name' => 'Ege Seramik', 'country' => 'Türkiye', 'website' => 'https://www.egeseramik.com'],
            ['slug' => 'marazzi', 'name' => 'Marazzi', 'country' => 'İtalya', 'website' => 'https://www.marazzi.it'],
            ['slug' => 'grohe', 'name' => 'Grohe', 'country' => 'Almanya', 'website' => 'https://www.grohe.com.tr'],
        ];

        foreach ($brands as $i => $brand) {
            Brand::updateOrCreate(['slug' => $brand['slug']], $brand + [
                'position' => $i + 1,
                'is_active' => true,
            ]);
        }
    }

    protected function products(): void
    {
        $categories = Category::pluck('id', 'slug');
        $brands = Brand::pluck('id', 'slug');

        // [slug, kategori, marka, ad TR, ad EN, kuruş fiyat, birim, görsel]
        $products = [
            ['travertine-60x60', 'seramik', 'seranit', 'Travertine 60×60 Porselen Karo', 'Travertine 60×60 Porcelain Tile', 89000, 'm²', 'prod-1'],
            ['calacatta-120x60', 'seramik', 'marazzi', 'Calacatta 120×60 Büyük Ebat', 'Calacatta 120×60 Large Format', 164000, 'm²', 'prod-3'],
            ['terrazzo-30x60', 'seramik', 'ege-seramik', 'Terrazzo 30×60 Duvar Karosu', 'Terrazzo 30×60 Wall Tile', 62000, 'm²', 'prod-4'],
            ['emperador-mermer', 'mermer', null, 'Emperador Doğal Mermer Levha', 'Emperador Natural Marble Slab', 245000, 'm²', 'prod-5'],
            ['statuario-tezgah', 'mermer', null, 'Statuario Tezgâh Uygulaması', 'Statuario Worktop', 310000, 'm²', 'prod-6'],
            ['mese-lamine-parke', 'parke', null, 'Meşe Lamine Parke 14 mm', 'Oak Engineered Flooring 14 mm', 128000, 'm²', 'prod-7'],
            ['ceviz-masif-parke', 'parke', null, 'Ceviz Masif Parke 18 mm', 'Walnut Solid Flooring 18 mm', 198000, 'm²', 'prod-8'],
            ['asma-klozet-seti', 'tuvalet', 'vitra', 'Asma Klozet ve Gömme Rezervuar Seti', 'Wall-hung WC and Concealed Cistern Set', 1450000, 'takım', 'prod-2'],
        ];

        foreach ($products as $i => [$slug, $categorySlug, $brandSlug, $nameTr, $nameEn, $price, $unit, $image]) {
            Product::updateOrCreate(['slug' => $slug], [
                'category_id' => $categories[$categorySlug],
                'brand_id' => $brandSlug ? $brands[$brandSlug] : null,
                'name' => ['tr' => $nameTr, 'en' => $nameEn],
                'summary' => [
                    'tr' => 'Yer tutucu ürün açıklaması. Gerçek teknik bilgi yönetim panelinden girilecek.',
                    'en' => 'Placeholder product description. Real technical detail will be entered from the admin panel.',
                ],
                'price' => $price,
                'currency' => 'TRY',
                'unit' => $unit,
                'stock' => 40 + $i * 7,
                'images' => ["media/{$image}.jpg"],
                'specs' => [
                    ['label' => 'Yüzey', 'value' => 'Mat'],
                    ['label' => 'Kullanım', 'value' => 'İç mekân · Islak hacim'],
                ],
                'position' => $i + 1,
                'is_active' => true,
                'is_featured' => $i < 4,
            ]);
        }
    }

    protected function projects(): void
    {
        $projects = [
            ['girne-konut', 'Girne Konut Projesi', 'Kyrenia Residence', 'Girne', 2025, 'proj-1'],
            ['lefkosa-otel', 'Lefkoşa Butik Otel', 'Nicosia Boutique Hotel', 'Lefkoşa', 2024, 'proj-2'],
            ['magusa-ofis', 'Gazimağusa Ofis Katı', 'Famagusta Office Floor', 'Gazimağusa', 2024, 'proj-3'],
            ['iskele-villa', 'İskele Villa Uygulaması', 'İskele Villa', 'İskele', 2023, 'proj-4'],
        ];

        foreach ($projects as $i => [$slug, $titleTr, $titleEn, $location, $year, $image]) {
            Project::updateOrCreate(['slug' => $slug], [
                'title' => ['tr' => $titleTr, 'en' => $titleEn],
                'summary' => [
                    'tr' => 'Yer tutucu referans metni. Gerçek proje bilgisi yönetim panelinden girilecek.',
                    'en' => 'Placeholder project summary. Real project detail will be entered from the admin panel.',
                ],
                'location' => $location,
                'year' => $year,
                'cover' => "media/{$image}.jpg",
                'position' => $i + 1,
                'is_active' => true,
                'is_featured' => $i < 4,
            ]);
        }
    }
}
