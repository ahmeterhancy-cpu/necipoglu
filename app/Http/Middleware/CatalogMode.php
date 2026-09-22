<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Katalog modunda sipariş akışını (sepet, ödeme, üyelik, hesap) kapatır.
 */
class CatalogMode
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_if(config('commerce.catalog'), 404);

        return $next($request);
    }
}
