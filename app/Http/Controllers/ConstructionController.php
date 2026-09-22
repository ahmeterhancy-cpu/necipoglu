<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ConstructionGate;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConstructionController extends Controller
{
    /** PIN doğruysa bu tarayıcıya bir yıllık önizleme çerezi bırakır. */
    public function unlock(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'pin' => ['required', 'string', 'max:32'],
            'to' => ['nullable', 'string', 'max:500'],
        ]);

        // Yalnızca site içi yola dön; "//baska-site.com" gibi dış adresler elenir.
        $to = $data['to'] ?? '/';
        $to = str_starts_with($to, '/') && ! str_starts_with($to, '//') ? $to : '/';

        $pin = (string) Setting::get('construction_pin');

        if ($pin === '' || ! hash_equals($pin, trim($data['pin']))) {
            return redirect($to)->with('construction.error', true);
        }

        return redirect($to)->withCookie(cookie(
            ConstructionGate::COOKIE,
            ConstructionGate::token($pin),
            60 * 24 * 365,
        ));
    }
}
