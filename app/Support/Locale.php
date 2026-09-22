<?php

namespace App\Support;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

/**
 * Aynı route dosyası iki kez kaydedilir: Türkçe adsız, İngilizce "en." önekli.
 * Bu sınıf, görünümlerin hangi dilde olduğunu düşünmeden link üretmesini sağlar.
 */
class Locale
{
    /** Şu anki dilin route adı öneki ("" ya da "en."). */
    public static function prefix(?string $locale = null): string
    {
        $locale ??= App::getLocale();

        return $locale === config('site.default_locale') ? '' : $locale.'.';
    }

    /**
     * Şu anki dilde adlandırılmış route URL'i.
     * $parameters, Laravel'in route() yardımcısıyla aynı esnekliktedir —
     * dizi, model ya da tek bir değer alabilir.
     */
    public static function route(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        return route(static::prefix().$name, $parameters, $absolute);
    }

    /** Belirli bir dilde adlandırılmış route URL'i (dil değiştirici için). */
    public static function routeIn(string $locale, string $name, mixed $parameters = [], bool $absolute = true): string
    {
        return route(static::prefix($locale).$name, $parameters, $absolute);
    }

    /**
     * Geçerli sayfanın başka bir dildeki karşılığı.
     * Route adı ve parametreleri korunur; eşleşme yoksa o dilin ana sayfasına düşer.
     */
    public static function currentIn(string $locale): string
    {
        $current = Route::currentRouteName();

        if ($current === null) {
            return static::routeIn($locale, 'home');
        }

        // Mevcut adın dil önekini soyup hedef dilinkini geçir.
        $bare = preg_replace('/^(?:'.implode('|', array_keys(config('site.locales'))).')\./', '', $current);
        $target = static::prefix($locale).$bare;

        if (! Route::has($target)) {
            return static::routeIn($locale, 'home');
        }

        return route($target, Route::current()?->parameters() ?? [], true);
    }

    /** <link rel="alternate" hreflang> için tüm dillerin URL'leri. */
    public static function alternates(): array
    {
        $out = [];

        foreach (array_keys(config('site.locales')) as $locale) {
            $out[$locale] = static::currentIn($locale);
        }

        return $out;
    }
}
