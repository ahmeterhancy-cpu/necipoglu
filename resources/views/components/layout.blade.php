@use('App\Support\Locale')
@use('App\Models\Setting')

@props([
    'title' => null,
    'description' => null,
    'ogImage' => null,
    'schema' => [],
])

@php
    $locales = config('site.locales');
    $current = app()->getLocale();
    $siteName = Setting::get('site_name', config('site.company.short_name'));

    $metaTitle = $title
        ? $title.' — '.$siteName
        : (Setting::get('meta_title') ?: $siteName);

    $metaDescription = $description
        ?: (Setting::get('meta_description') ?: __('site.footer.tagline'));

    $alternates = Locale::alternates();

    // Yapısal veri: her sayfada kurum + site, üstüne sayfaya özel düğümler.
    $schemaGraph = array_merge(\App\Support\Schema::base(), $schema);
@endphp

<!doctype html>
<html lang="{{ $locales[$current]['html'] ?? $current }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="theme-color" content="#17171A">

    <link rel="canonical" href="{{ url()->current() }}">

    @foreach ($alternates as $code => $href)
        <link rel="alternate" hreflang="{{ $locales[$code]['html'] ?? $code }}" href="{{ $href }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $alternates[config('site.default_locale')] ?? url('/') }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
        <meta name="twitter:card" content="summary_large_image">
    @endif

    {{-- Sekme ikonu: yeni logodan türetilmiş CN işareti (antrasit + mavi kanca). --}}
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('brand/icon-32.png') }}" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('brand/icon-180.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- GÜVENLİK AĞI — içerik gizli kalmasın.
         [data-reveal] öğeleri açılışta opacity:0 ile duruyor ve yalnızca
         hareket motoru .is-in eklediğinde görünür oluyor. Motor herhangi
         bir sebeple çalışmazsa (JS kapalı, dosya yüklenmedi, betik hatası)
         sayfanın tamamı KALICI OLARAK boş kalırdı.
         Aşağıdaki iki önlem bunu kapatıyor. --}}
    <noscript>
        <style>
            [data-reveal], [data-draw] { opacity: 1 !important; transform: none !important; }
            [data-veil] > img, [data-veil] > video, [data-veil] > .veil-inner { clip-path: none !important; transform: none !important; }
            .m-word { transform: none !important; }
        </style>
    </noscript>

    <script>
        /* JS açık ama motor 2,5 saniyede kendini bildirmediyse
           içeriği olduğu gibi göster. */
        setTimeout(function () {
            var k = document.documentElement.classList;
            if (!k.contains('motion-ready')) { k.add('motion-ready', 'motion-failed'); }
        }, 2500);
    </script>

    <script type="application/ld+json">{!! \App\Support\Schema::render($schemaGraph) !!}</script>

    {{ $head ?? '' }}
</head>

<body>
    {{-- Sayfa geçiş perdesi --}}
    <div class="veil" aria-hidden="true"></div>

    <a href="#main" class="sr-only focus:not-sr-only">{{ __('site.nav.skip') }}</a>

    @include('partials.topbar')
    @include('partials.menu')

    <main id="main">
        {{ $slot }}
    </main>

    @include('partials.footer')
    @include('partials.whatsapp')
</body>
</html>
