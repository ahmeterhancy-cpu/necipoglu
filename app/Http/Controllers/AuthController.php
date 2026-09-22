<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Müşteri girişi ve kaydı. Yönetim paneli ayrı (Filament) — buradaki
 * hesaplar yalnızca alışveriş içindir.
 */
class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('pages.account.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($data, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('site.account.bad_credentials')]);
        }

        // Oturum sabitleme saldırısına karşı kimliği yenile.
        $request->session()->regenerate();

        return redirect()->intended(Locale::route('account'));
    }

    public function showRegister(): View
    {
        return view('pages.account.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:40'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'website' => ['prohibited'],   // bal küpü
        ], [
            'website.prohibited' => __('site.contact.spam'),
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect(Locale::route('account'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(Locale::route('home'));
    }
}
