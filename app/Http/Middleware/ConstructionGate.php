<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Yapım aşaması kapısı. Panelden açıldığında ziyaretçi sitenin yerine
 * "yapım aşamasında" sayfasını görür. O sayfada doğru PIN girilen tarayıcıya
 * bir çerez bırakılır; site yalnızca o cihazda normal açılır.
 *
 * Çerezin değeri PIN'in kendisi değil, uygulama anahtarıyla imzalanmış
 * özeti. PIN panelden değiştirildiğinde eski çerezlerin hepsi geçersiz kalır.
 */
class ConstructionGate
{
    public const COOKIE = 'cn_onizleme';

    /**
     * Kapının hiç bakmadığı yollar: panel, Livewire, PIN formu, sağlık kontrolü.
     *
     * 'livewire*' eğik çizgisiz: Filament Livewire'ı rastgele önekle servis
     * ediyor (/livewire-172643c6/update). 'livewire/*' yazılırsa kapı açıkken
     * paneldeki her kaydet düğmesi sessizce bu sayfaya çarpar.
     */
    protected const OPEN = ['admin', 'admin/*', 'livewire*', 'filament/*', 'up', 'onizleme-pin'];

    public function handle(Request $request, Closure $next): Response
    {
        if (! Setting::get('construction_enabled') || $request->is(...self::OPEN)) {
            return $next($request);
        }

        // Panele giriş yapmış yönetici siteyi her zaman görür.
        if ($request->user()?->is_admin || static::hasValidCookie($request)) {
            return $next($request);
        }

        // Bu ara katman rota ara katmanlarından (SetLocale) önce çalışıyor;
        // İngilizce adresten gelene sayfa İngilizce gösterilsin.
        if ($request->segment(1) === 'en') {
            app()->setLocale('en');
        }

        return response()
            ->view('construction', [
                'branches' => Branch::query()->active()->ordered()->get(),
                'to' => '/'.ltrim($request->path(), '/'),
            ], 503)
            ->header('Retry-After', '86400')
            ->header('X-Robots-Tag', 'noindex, nofollow')
            ->header('Cache-Control', 'no-store');
    }

    public static function token(string $pin): string
    {
        return hash_hmac('sha256', 'onizleme|'.$pin, (string) config('app.key'));
    }

    public static function hasValidCookie(Request $request): bool
    {
        $pin = (string) Setting::get('construction_pin');
        $cookie = (string) $request->cookie(self::COOKIE);

        return $pin !== '' && $cookie !== '' && hash_equals(static::token($pin), $cookie);
    }
}
