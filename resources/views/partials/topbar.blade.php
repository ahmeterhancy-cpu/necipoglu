@use('App\Support\Locale')

<header class="topbar">
    <div class="shell topbar-inner">
        <a href="{{ Locale::route('home') }}" class="topbar-logo" aria-label="{{ __('site.nav.home') }}">
            <img src="{{ asset('brand/logo-light.png') }}" class="logo-light" alt="Cahit Necipoğlu" width="724" height="188">
            <img src="{{ asset('brand/logo-dark.png') }}" class="logo-dark" alt="" aria-hidden="true" width="724" height="188">
        </a>

        <nav class="nav" aria-label="{{ __('site.nav.menu') }}">
            <a href="{{ Locale::route('shop.index') }}" @if (request()->routeIs('*shop.*')) aria-current="page" @endif>{{ __('site.nav.shop') }}</a>
            <a href="{{ Locale::route('brands') }}" @if (request()->routeIs('*brands')) aria-current="page" @endif>{{ __('site.nav.brands') }}</a>
            <a href="{{ Locale::route('projects.index') }}" @if (request()->routeIs('*projects.*')) aria-current="page" @endif>{{ __('site.nav.projects') }}</a>
            <a href="{{ Locale::route('tv') }}" @if (request()->routeIs('*tv')) aria-current="page" @endif>{{ __('site.nav.tv') }}</a>
            <a href="{{ Locale::route('about') }}" @if (request()->routeIs('*about')) aria-current="page" @endif>{{ __('site.nav.about') }}</a>
            <a href="{{ Locale::route('market') }}" @if (request()->routeIs('*market')) aria-current="page" @endif>{{ __('site.nav.market') }}</a>
            <a href="{{ (\App\Models\Setting::get('dura_coffee_url') ?: config('site.dura_coffee')) }}" target="_blank" rel="noopener" data-no-veil>{{ __('site.nav.dura') }}</a>
            <a href="{{ Locale::route('blog.index') }}" @if (request()->routeIs('*blog.*')) aria-current="page" @endif>{{ __('site.nav.blog') }}</a>
            <a href="{{ Locale::route('contact') }}" @if (request()->routeIs('*contact')) aria-current="page" @endif>{{ __('site.nav.contact') }}</a>
        </nav>

        {{-- shrink-0: menü uzadığında flex bu kümeyi eziyor, hesap ve sepet
             simgeleri 0 piksel genişliğe düşüp kayboluyordu. --}}
        <div class="flex shrink-0 items-center gap-4">
            {{-- Hesap ve sepet — sepet sayısı yalnızca dolu sepette görünür.
                 Katalog modunda ikisi de yok: sipariş alınmıyor. --}}
            @unless (config('commerce.catalog'))
            <a href="{{ auth()->check() ? Locale::route('account') : Locale::route('login') }}"
               class="eyebrow opacity-85 transition-opacity hover:opacity-100"
               aria-label="{{ auth()->check() ? __('site.account.title') : __('site.account.login') }}">
                <svg viewBox="0 0 20 20" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.35" aria-hidden="true">
                    <circle cx="10" cy="6.5" r="3.2"/>
                    <path d="M3.5 17c0-3.3 2.9-5.6 6.5-5.6s6.5 2.3 6.5 5.6"/>
                </svg>
            </a>

            <a href="{{ Locale::route('cart') }}"
               class="eyebrow relative flex items-center gap-1.5 opacity-85 transition-opacity hover:opacity-100"
               aria-label="{{ __('site.cart.title') }}">
                <svg viewBox="0 0 20 20" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.35" aria-hidden="true">
                    <path d="M3 4h2l1.6 8.6a1.5 1.5 0 0 0 1.5 1.2h6.3a1.5 1.5 0 0 0 1.5-1.2L17 7H5.5"/>
                    <circle cx="8.5" cy="17" r="1"/>
                    <circle cx="14.5" cy="17" r="1"/>
                </svg>

                @if ($cartCount = app(\App\Services\CartService::class)->count())
                    <span class="tnum">{{ $cartCount }}</span>
                @endif
            </a>
            @endunless

            <div class="eyebrow flex items-center gap-2 text-current" role="group" aria-label="Language">
                @foreach (config('site.locales') as $code => $meta)
                    @if ($code === app()->getLocale())
                        <span aria-current="true">{{ $meta['short'] }}</span>
                    @else
                        <a href="{{ Locale::currentIn($code) }}" hreflang="{{ $meta['html'] }}" class="opacity-60 transition-opacity hover:opacity-100">{{ $meta['short'] }}</a>
                    @endif
                @endforeach
            </div>

            <button type="button"
                    class="menu-toggle"
                    data-menu-toggle
                    aria-expanded="false"
                    aria-controls="site-menu"
                    aria-label="{{ __('site.nav.menu') }}">
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>
