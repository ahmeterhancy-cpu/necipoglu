@use('App\Support\Locale')

<div class="menu-panel" id="site-menu" aria-hidden="true">
    <div class="shell">
        <p class="eyebrow eyebrow-mute mb-8">{{ __('site.nav.menu') }}</p>

        <nav aria-label="{{ __('site.nav.menu') }}">
            @foreach ([
                'shop.index' => __('site.nav.shop'),
                'brands' => __('site.nav.brands'),
                'projects.index' => __('site.nav.projects'),
                'tv' => __('site.nav.tv'),
                'about' => __('site.nav.about'),
                'market' => __('site.nav.market'),
                (\App\Models\Setting::get('dura_coffee_url') ?: config('site.dura_coffee')) => __('site.nav.dura'),
                'blog.index' => __('site.nav.blog'),
                'contact' => __('site.nav.contact'),
            ] as $route => $label)
                {{-- Anahtar ya rota adı ya da dış adres (Dura Coffee). --}}
                @if (str_starts_with($route, 'http'))
                    <a href="{{ $route }}" class="menu-link" target="_blank" rel="noopener" data-no-veil>{{ $label }}</a>
                @else
                    <a href="{{ Locale::route($route) }}" class="menu-link">{{ $label }}</a>
                @endif
            @endforeach
        </nav>

        <div class="mt-9 flex flex-wrap gap-x-8 gap-y-3">
            @foreach (config('site.locales') as $code => $meta)
                <a href="{{ Locale::currentIn($code) }}" hreflang="{{ $meta['html'] }}" class="eyebrow eyebrow-mute link">{{ $meta['name'] }}</a>
            @endforeach
        </div>
    </div>
</div>
