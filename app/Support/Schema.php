<?php

namespace App\Support;

use App\Models\Branch;
use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use App\Models\Setting;

/**
 * JSON-LD yapısal veri üreticisi.
 *
 * Google'ın zengin sonuç çıkarabilmesi için sayfa türüne göre schema.org
 * grafiği üretir. Uydurma veri ÜRETİLMEZ: fiyat, stok, adres ve telefon
 * ne varsa veritabanından gelir; olmayan alan hiç yazılmaz — eksik alan,
 * yanlış alandan iyidir.
 */
class Schema
{
    /** Her sayfada bulunan temel grafik: kurum + site araması. */
    public static function base(): array
    {
        $name = Setting::get('site_name', config('site.company.short_name'));
        $legal = Setting::get('legal_name', config('site.company.legal_name'));

        $organisation = array_filter([
            '@type' => 'Organization',
            '@id' => url('/').'#organization',
            'name' => $name,
            'legalName' => $legal,
            'url' => url('/'),
            'logo' => asset('brand/logo-dark.png'),
            'foundingDate' => Setting::get('founded_year'),
            'email' => Setting::get('email', config('site.company.email')),
            'sameAs' => array_values(array_filter([
                Setting::get('social_instagram'),
                Setting::get('social_facebook'),
                Setting::get('social_youtube'),
                Setting::get('social_linkedin'),
            ])),
            'areaServed' => 'Kuzey Kıbrıs',
        ]);

        // Boş sameAs dizisi çıkmasın.
        if (empty($organisation['sameAs'])) {
            unset($organisation['sameAs']);
        }

        return [
            $organisation,
            [
                '@type' => 'WebSite',
                '@id' => url('/').'#website',
                'url' => url('/'),
                'name' => $name,
                'inLanguage' => app()->getLocale(),
                'publisher' => ['@id' => url('/').'#organization'],
            ],
        ];
    }

    /** Şube — fiziksel mağaza. İletişim sayfasında her şube için bir tane. */
    public static function branch(Branch $branch): array
    {
        return array_filter([
            '@type' => 'HomeGoodsStore',
            '@id' => url('/').'#branch-'.$branch->slug,
            'name' => Setting::get('site_name', config('site.company.short_name')).' — '.$branch->name,
            'parentOrganization' => ['@id' => url('/').'#organization'],
            'address' => array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $branch->address,
                'addressLocality' => $branch->city,
                'addressCountry' => 'CY',
            ]),
            'telephone' => $branch->primaryPhone(),
            'email' => $branch->email,
            'geo' => ($branch->lat && $branch->lng) ? [
                '@type' => 'GeoCoordinates',
                'latitude' => $branch->lat,
                'longitude' => $branch->lng,
            ] : null,
        ], fn ($v) => $v !== null && $v !== '' && $v !== []);
    }

    /** Ürün — fiyat ve stok durumu dahil. */
    public static function product(Product $product): array
    {
        $availability = $product->isInStock()
            ? 'https://schema.org/InStock'
            : 'https://schema.org/OutOfStock';

        return array_filter([
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->summary,
            'sku' => $product->sku,
            'image' => $product->coverImage() ? url(\Illuminate\Support\Facades\Storage::url($product->coverImage())) : null,
            'category' => $product->category?->name,
            'brand' => $product->brand ? ['@type' => 'Brand', 'name' => $product->brand->name] : null,
            'offers' => [
                '@type' => 'Offer',
                'url' => url()->current(),
                // schema.org fiyatı ondalık ister; veritabanı kuruş tutuyor.
                'price' => number_format($product->price / 100, 2, '.', ''),
                'priceCurrency' => $product->currency,
                'availability' => $availability,
                'seller' => ['@id' => url('/').'#organization'],
            ],
        ], fn ($v) => $v !== null && $v !== '');
    }

    /** Referans projesi. */
    public static function project(Project $project): array
    {
        return array_filter([
            '@type' => 'CreativeWork',
            'name' => $project->title,
            'description' => $project->summary,
            'url' => url()->current(),
            'image' => $project->cover ? url(\Illuminate\Support\Facades\Storage::url($project->cover)) : null,
            'dateCreated' => $project->year ? (string) $project->year : null,
            'locationCreated' => $project->location ? ['@type' => 'Place', 'name' => $project->location] : null,
            'creator' => ['@id' => url('/').'#organization'],
        ], fn ($v) => $v !== null && $v !== '');
    }

    /** Blog yazısı. */
    public static function article(Post $post): array
    {
        return array_filter([
            '@type' => 'Article',
            'headline' => $post->title,
            'description' => $post->excerpt,
            'url' => url()->current(),
            'image' => $post->cover ? url(\Illuminate\Support\Facades\Storage::url($post->cover)) : null,
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'author' => $post->author ? ['@type' => 'Person', 'name' => $post->author] : ['@id' => url('/').'#organization'],
            'publisher' => ['@id' => url('/').'#organization'],
            'inLanguage' => app()->getLocale(),
        ], fn ($v) => $v !== null && $v !== '');
    }

    /**
     * Kırıntı yolu.
     *
     * @param  array<int, array{name: string, url: string}>  $trail
     */
    public static function breadcrumbs(array $trail): array
    {
        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($trail)->values()->map(fn ($item, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ])->all(),
        ];
    }

    /** Grafiği tek bir JSON-LD betiğine çevirir. */
    public static function render(array $graph): string
    {
        return json_encode(
            ['@context' => 'https://schema.org', '@graph' => array_values(array_filter($graph))],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        );
    }
}
