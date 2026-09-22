<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Türkçe kök dizinde (/), İngilizce /en önekinde servis edilir.
 * Önek ve route adı öneki route grubundan gelir; burada yalnızca
 * uygulama dilini sabitliyoruz.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next, ?string $locale = null): Response
    {
        if (! array_key_exists($locale, config('site.locales'))) {
            $locale = config('site.default_locale');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
