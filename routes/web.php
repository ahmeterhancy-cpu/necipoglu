<?php

use App\Http\Controllers\ConstructionController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

/*
| Site route'ları tek dosyada tanımlanır ve her dil için bir kez kaydedilir.
| Türkçe kök dizinde ve adsız, İngilizce /en önekinde ve "en." adıyla.
|
| routes/site.php bir closure döndürür; closure'a dil kodu geçildiği için
| her dilin URL segmentleri kendi dilinde olabilir (/hakkimizda ↔ /en/about).
| Görünümler link üretirken App\Support\Locale::route() kullanır.
*/

$default = config('site.default_locale');

foreach (array_keys(config('site.locales')) as $locale) {
    $register = require base_path('routes/site.php');

    Route::prefix($locale === $default ? '' : $locale)
        ->name($locale === $default ? '' : $locale.'.')
        ->middleware(SetLocale::class.':'.$locale)
        ->group(fn () => $register($locale));
}

/*
| sitemap.xml dile göre çoğaltılmaz: tek dosya, içinde her URL'in tüm
| dillerdeki karşılığı hreflang ile veriliyor.
*/
Route::get('sitemap.xml', SitemapController::class)->name('sitemap');

/*
| Yapım aşaması sayfasındaki PIN formu. Dile bağlı değil; kapı bu yolu
| hiç kontrol etmez (App\Http\Middleware\ConstructionGate::OPEN).
*/
Route::post('onizleme-pin', [ConstructionController::class, 'unlock'])
    ->middleware('throttle:5,1')
    ->name('construction.unlock');
