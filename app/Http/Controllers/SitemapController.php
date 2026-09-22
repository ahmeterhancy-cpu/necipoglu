<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use App\Support\Locale;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

/**
 * sitemap.xml — her URL'in tüm dillerdeki karşılığı hreflang ile birlikte
 * verilir, böylece Google TR ve EN sayfaları ayrı içerik değil aynı sayfanın
 * çevirisi olarak değerlendirir.
 *
 * Sepet, ödeme ve hesap sayfaları dışarıda bırakılır — dizine girmemeleri
 * gerekir.
 */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $entries = [];

        // Sabit sayfalar
        foreach ([
            'home' => ['1.0', 'weekly'],
            'shop.index' => ['0.9', 'weekly'],
            'about' => ['0.7', 'monthly'],
            'brands' => ['0.7', 'monthly'],
            'projects.index' => ['0.7', 'monthly'],
            'tv' => ['0.6', 'monthly'],
            'blog.index' => ['0.6', 'weekly'],
            'market' => ['0.7', 'monthly'],
            'contact' => ['0.8', 'monthly'],
        ] as $name => [$priority, $frequency]) {
            $entries[] = $this->entry($name, [], null, $priority, $frequency);
        }

        foreach (Category::query()->active()->ordered()->get() as $category) {
            $entries[] = $this->entry('shop.category', $category, $category->updated_at, '0.8', 'weekly');
        }

        foreach (Product::query()->active()->ordered()->get() as $product) {
            $entries[] = $this->entry('shop.product', $product, $product->updated_at, '0.7', 'weekly');
        }

        foreach (Brand::query()->active()->ordered()->get() as $brand) {
            $entries[] = $this->entry('brands.show', $brand, $brand->updated_at, '0.6', 'monthly');
        }

        foreach (Project::query()->active()->ordered()->get() as $project) {
            $entries[] = $this->entry('projects.show', $project, $project->updated_at, '0.6', 'monthly');
        }

        foreach (Post::query()->published()->latestFirst()->get() as $post) {
            $entries[] = $this->entry('blog.show', $post, $post->updated_at, '0.5', 'monthly');
        }

        return response()
            ->view('sitemap', ['entries' => array_filter($entries)])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * Tek bir URL girdisi: varsayılan dildeki adres + tüm dillerin alternatifi.
     * Route bir dilde tanımlı değilse girdi hiç üretilmez.
     */
    protected function entry(string $name, mixed $parameters, $lastmod, string $priority, string $frequency): ?array
    {
        $default = config('site.default_locale');
        $alternates = [];

        foreach (array_keys(config('site.locales')) as $locale) {
            $routeName = Locale::prefix($locale).$name;

            if (! Route::has($routeName)) {
                continue;
            }

            $alternates[$locale] = route($routeName, $parameters);
        }

        if (! isset($alternates[$default])) {
            return null;
        }

        return [
            'loc' => $alternates[$default],
            'lastmod' => $lastmod?->toAtomString(),
            'priority' => $priority,
            'changefreq' => $frequency,
            'alternates' => $alternates,
        ];
    }
}
