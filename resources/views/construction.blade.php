@use('App\Models\Setting')

{{-- Yapım aşaması sayfası. Sitenin ortak iskeletini (üst çubuk, menü, alt
     bilgi) bilerek kullanmıyor: o bağlantıların hepsi yine bu sayfaya döner.
     Ziyaretçiye yalnızca şubelere ulaşma yolu ve yetkili için PIN alanı kalır. --}}

@php
    $title = Setting::get('construction_title') ?: __('site.construction.title');
    $body = Setting::get('construction_body') ?: __('site.construction.body');
    $whatsapp = preg_replace('/[^\d]/', '', (string) Setting::get('whatsapp', config('site.company.whatsapp')));
    $error = session('construction.error');
@endphp

<!doctype html>
<html lang="{{ config('site.locales.'.app()->getLocale().'.html', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#383E42">

    <title>{{ Setting::get('site_name', config('site.company.short_name')) }}</title>

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    {{-- Yalnızca stil: hareket motoru yüklenmez, içerik gizlenip gösterilmez. --}}
    @vite(['resources/css/app.css'])
</head>
<body class="surface-ink">
    <main class="shell flex min-h-svh flex-col py-[clamp(1.5rem,4vw,3rem)]">

        <img src="{{ asset('brand/logo-light.png') }}"
             alt="Cahit Necipoğlu"
             width="425" height="59"
             class="h-auto w-[clamp(9rem,16vw,12rem)]">

        <div class="grid12 my-auto items-end gap-y-12 py-[clamp(3rem,8vw,6rem)]">

            <div class="col-span-12 lg:col-span-7">
                <p class="eyebrow">{{ __('site.construction.eyebrow') }}</p>
                <h1 class="h1 mt-6 max-w-[16ch]">{{ $title }}</h1>
                <p class="lede mt-7 max-w-[46ch]">{{ $body }}</p>

                @if ($whatsapp)
                    <div class="mt-9 flex flex-wrap gap-3">
                        <a href="https://wa.me/{{ $whatsapp }}" class="btn btn-line" target="_blank" rel="noopener">
                            {{ __('site.brand.whatsapp') }}
                        </a>
                    </div>
                @endif
            </div>

            @if ($branches->isNotEmpty())
                <div class="col-span-12 lg:col-span-4 lg:col-start-9">
                    <dl class="list">
                        @foreach ($branches as $branch)
                            <div class="list-row">
                                <span>
                                    <dt class="list-title block">{{ $branch->name }}</dt>
                                    <dd class="list-note block">{{ $branch->address }}</dd>
                                </span>

                                @if ($phone = ($branch->phones[0] ?? null))
                                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}"
                                       class="list-meta tnum shrink-0">{{ $phone }}</a>
                                @endif
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endif
        </div>

        {{-- Yetkili girişi: kapalı durur, hatalı PIN'den sonra açık gelir. --}}
        <details @if ($error) open @endif>
            <summary class="eyebrow eyebrow-mute cursor-pointer select-none">{{ __('site.construction.staff') }}</summary>

            <form method="post" action="{{ route('construction.unlock') }}" class="mt-5 flex max-w-sm flex-wrap items-stretch gap-3">
                @csrf
                <input type="hidden" name="to" value="{{ $to }}">

                <label class="sr-only" for="pin">{{ __('site.construction.pin') }}</label>
                <input id="pin" name="pin" type="password" inputmode="numeric" autocomplete="off" required
                       placeholder="{{ __('site.construction.pin') }}"
                       class="min-w-0 flex-1 rounded-md border border-current/25 bg-transparent px-4 py-3 tnum outline-none transition-colors focus:border-current"
                       @if ($error) autofocus aria-invalid="true" aria-describedby="pin-error" @endif>

                <button type="submit" class="btn btn-line">{{ __('site.construction.enter') }}</button>

                @if ($error)
                    <p id="pin-error" class="body-s w-full" style="color:#E3B58A">{{ __('site.construction.wrong') }}</p>
                @endif
            </form>
        </details>
    </main>
</body>
</html>
