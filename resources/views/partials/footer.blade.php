@use('App\Support\Locale')
@use('App\Models\Branch')
@use('App\Models\Setting')

@php
    $branches = Branch::query()->active()->ordered()->get();
    $social = collect([
        'Instagram' => Setting::get('social_instagram'),
        'Facebook' => Setting::get('social_facebook'),
        'YouTube' => Setting::get('social_youtube'),
        'LinkedIn' => Setting::get('social_linkedin'),
    ])->filter();
@endphp

<footer class="surface-ink-2 pt-[clamp(3.5rem,7vw,6rem)]">
    <div class="shell">
        <div class="grid12 gap-y-12">
            <div class="col-span-12 lg:col-span-4">
                <img src="{{ asset('brand/logo-light.png') }}" alt="Cahit Necipoğlu" width="724" height="188" class="h-6 w-auto">
                <p class="body-m mt-6 max-w-[34ch]">{{ __('site.footer.tagline') }}</p>

                @if ($social->isNotEmpty())
                    <div class="mt-8 flex flex-wrap gap-x-6 gap-y-3">
                        @foreach ($social as $name => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener" data-no-veil class="eyebrow eyebrow-mute link">{{ $name }}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="col-span-6 md:col-span-3 lg:col-span-2">
                <p class="eyebrow">{{ __('site.footer.explore') }}</p>
                <ul class="mt-6 space-y-3 body-s">
                    <li><a href="{{ Locale::route('shop.index') }}" class="link">{{ __('site.nav.shop') }}</a></li>
                    <li><a href="{{ Locale::route('brands') }}" class="link">{{ __('site.nav.brands') }}</a></li>
                    <li><a href="{{ Locale::route('projects.index') }}" class="link">{{ __('site.nav.projects') }}</a></li>
                    <li><a href="{{ Locale::route('tv') }}" class="link">{{ __('site.nav.tv') }}</a></li>
                    <li><a href="{{ Locale::route('blog.index') }}" class="link">{{ __('site.nav.blog') }}</a></li>
                </ul>
            </div>

            <div class="col-span-6 md:col-span-3 lg:col-span-2">
                <p class="eyebrow">{{ __('site.footer.corporate') }}</p>
                <ul class="mt-6 space-y-3 body-s">
                    <li><a href="{{ Locale::route('about') }}" class="link">{{ __('site.nav.about') }}</a></li>
                    <li><a href="{{ Locale::route('market') }}" class="link">{{ __('site.nav.market') }}</a></li>
                    <li><a href="{{ (\App\Models\Setting::get('dura_coffee_url') ?: config('site.dura_coffee')) }}" target="_blank" rel="noopener" data-no-veil class="link">{{ __('site.nav.dura') }}</a></li>
                    <li><a href="{{ Locale::route('contact') }}" class="link">{{ __('site.nav.contact') }}</a></li>
                </ul>
            </div>

            <div class="col-span-12 lg:col-span-4">
                <p class="eyebrow">{{ __('site.common.branches') }}</p>

                <ul class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-1">
                    @foreach ($branches as $branch)
                        <li>
                            <p class="body-s font-medium" style="color:var(--color-paper-2)">{{ $branch->name }} <span class="mute">· {{ $branch->kind }}</span></p>
                            <p class="body-s mt-1">{{ $branch->address }}</p>
                            @if ($phone = $branch->primaryPhone())
                                <a href="tel:{{ $branch->dialable($phone) }}" data-no-veil class="body-s link tnum mt-1 inline-block">{{ $phone }}</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <hr class="rule mt-[clamp(3rem,6vw,5rem)]">

        <div class="flex flex-wrap items-center justify-between gap-4 py-8">
            <span class="eyebrow eyebrow-mute">© {{ now()->year }} {{ Setting::get('legal_name', config('site.company.legal_name')) }}</span>
            <a href="mailto:{{ Setting::get('email', config('site.company.email')) }}" data-no-veil class="eyebrow link">
                {{ Setting::get('email', config('site.company.email')) }}
            </a>
        </div>
    </div>
</footer>
