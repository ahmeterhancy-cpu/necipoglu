<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

/**
 * GERÇEK markalar. Logolar müşteriden alınmış, public/brand/logos altında
 * markanın kısa adıyla duruyor.
 *
 * Ülke bilgisi yalnızca kesin bilinen markalarda doldurulmuştur; emin
 * olunmayanlar boş bırakıldı — yanlış ülke yazmaktansa boş bırakmak doğru.
 * Panelden tamamlanabilir.
 */
class BrandSeeder extends Seeder
{
    public function run(): void
    {
        // [kısa ad, görünen ad, ülke, web sitesi]
        $brands = [
            ['hansgrohe',          'hansgrohe',           'Almanya',  'https://www.hansgrohe.com.tr'],
            ['axor',               'AXOR',                'Almanya',  'https://www.axor-design.com'],
            ['duravit',            'Duravit',             'Almanya',  'https://www.duravit.com.tr'],
            ['geberit',            'Geberit',             'İsviçre',  'https://www.geberit.com.tr'],
            ['decor-walther',      'Decor Walther',       'Almanya',  'https://www.decor-walther.com'],
            ['aco',                'ACO',                 'Almanya',  'https://www.aco.com.tr'],
            ['gedy',               'Gedy',                'İtalya',   'https://www.gedy.com'],

            ['keraben',            'Keraben',             'İspanya',  'https://www.keraben.com'],
            ['ibero',              'Ibero',               'İspanya',  'https://www.iberoceramica.com'],
            ['el-molino',          'El Molino',           'İspanya',  'https://www.elmolino.es'],
            ['grespania',          'Grespania',           'İspanya',  'https://www.grespania.com'],
            ['alaplana',           'Alaplana',            'İspanya',  'https://www.alaplana.es'],
            ['dune',               'Dune',                'İspanya',  'https://www.dune.es'],
            ['sonia',              'Sonia',               'İspanya',  'https://www.sonia-sa.com'],
            ['platera',            'Platera',             'İspanya',  null],

            ['ng-kutahya-seramik', 'NG Kütahya Seramik',  'Türkiye',  'https://www.ngkutahyaseramik.com.tr'],
            ['anka-seramik',       'Anka Seramik',        'Türkiye',  null],
            ['denko',              'Denko',               'Türkiye',  null],
            ['kare',               'Kare',                'Türkiye',  null],
            ['eva-banyo',          'Eva Banyo',           'Türkiye',  null],
            ['teska',              'Teska',               'Türkiye',  null],

            // Ülkesinden emin olunamayanlar — panelden doldurulacak.
            ['sanacryl',           'Sanacryl',            null,       null],
            ['smanni',             'Smanni',              null,       null],
            ['luca',               'Luca',                null,       null],
            ['omg',                'omG',                 null,       null],
            ['cypdus',             'Cypdus',              null,       null],
            ['line',               'Line',                null,       null],
            ['windisch',           'Windisch',            null,       null],
            ['carrox',             'Carrox Phoenix',      null,       null],
            ['dura-bagno',         'Dura Bagno',          null,       null],
        ];

        // Ana sayfa duvarında görünecekler. Tamamı Markalar sayfasında.
        $featured = [
            'hansgrohe', 'axor', 'duravit', 'geberit', 'decor-walther', 'aco',
            'keraben', 'grespania', 'alaplana', 'ng-kutahya-seramik', 'anka-seramik', 'sonia',
        ];

        foreach ($brands as $i => [$slug, $name, $country, $website]) {
            Brand::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'country' => $country,
                'website' => $website,
                'position' => $i + 1,
                'is_active' => true,
                'is_featured' => in_array($slug, $featured, true),
            ]);
        }

        // Demo aşamasından kalan uydurma markaları temizle.
        Brand::whereNotIn('slug', array_column($brands, 0))->delete();
    }
}
