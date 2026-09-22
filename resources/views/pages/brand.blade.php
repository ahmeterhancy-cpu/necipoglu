@use('App\Support\Locale')
@use('App\Models\Setting')

@php
    $schema = [
        \App\Support\Schema::breadcrumbs([
            ['name' => __('site.nav.brands'), 'url' => Locale::route('brands')],
            ['name' => $brand->name, 'url' => url()->current()],
        ]),
    ];

    $whatsapp = preg_replace('/[^\d]/', '', (string) Setting::get('whatsapp', config('site.company.whatsapp')));
@endphp

<x-layout :title="$brand->name" :description="$brand->description" :schema="$schema">

    {{-- ═══ MARKA BAŞLIĞI ═══════════════════════════════════════════════════
         Logo tipografiyle yarışmasın diye kendi çerçevesinde durur; ad ve
         tanıtım yanında. Dış siteye çıkış TEK yerde — burada.
         ─────────────────────────────────────────────────────────────────── --}}
    <section class="surface-paper pt-[calc(var(--topbar)+clamp(2.5rem,5vw,4rem))] pb-[clamp(2.5rem,5vw,4rem)]">
        <div class="shell">

            <nav class="eyebrow eyebrow-mute" aria-label="breadcrumb" data-reveal="fade">
                <a href="{{ Locale::route('brands') }}" class="link">← {{ __('site.brand.back') }}</a>
            </nav>

            <div class="grid12 mt-8 items-center gap-y-8">

                <div class="col-span-12 sm:col-span-7 lg:col-span-4">
                    <div class="grid aspect-[3/2] place-items-center rounded-lg bg-paper-2 p-[clamp(1.5rem,3vw,2.5rem)]"
                         data-reveal="scale">
                        @if ($logo = $brand->logoUrl())
                            <img src="{{ $logo }}"
                                 alt="{{ $brand->name }}"
                                 fetchpriority="high"
                                 class="max-h-20 max-w-full w-auto object-contain">
                        @else
                            <span class="h3">{{ $brand->name }}</span>
                        @endif
                    </div>
                </div>

                <div class="col-span-12 lg:col-span-7 lg:col-start-6">
                    @if ($brand->country)
                        <p class="eyebrow eyebrow-mute" data-reveal="fade">{{ $brand->country }}</p>
                    @endif

                    <h1 class="h1 {{ $brand->country ? 'mt-4' : '' }}" data-split>{{ $brand->name }}</h1>

                    @if ($brand->description)
                        <p class="lede mute mt-5" data-reveal>{{ $brand->description }}</p>
                    @endif

                    @if ($gruplar = $brand->highlightList())
                        {{-- Markanin ne urettigi tek bakista gorunsun. Etiketler
                             markanin kendi urun sayfalarindan derlendi. --}}
                        <div class="mt-7" data-reveal>
                            <p class="eyebrow eyebrow-mute">{{ __('site.brand.groups') }}</p>

                            <ul class="mt-4 flex flex-wrap gap-x-2 gap-y-2">
                                @foreach ($gruplar as $grup)
                                    <li class="rounded-sm border border-line px-3 py-1.5 body-s">{{ $grup }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (filled($brand->website))
                        <div class="mt-8" data-reveal>
                            {{-- Dış bağlantı: yeni sekme + sayfa perdesi devre dışı. --}}
                            <a href="{{ $brand->website }}"
                               class="btn"
                               target="_blank"
                               rel="noopener"
                               data-no-veil>
                                {{ __('site.brand.website') }}
                                <svg viewBox="0 0 16 16" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path d="M6 3h7v7"/>
                                    <path d="M13 3 4 12"/>
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ UYGULAMALAR ═════════════════════════════════════════════════════
         Yerleşim görsel SAYISINA göre değişir. Sabit üç kartlık ray, iki
         görseli olan markada sağda boş bir delik bırakıyordu:
           1 görsel  → tek kare, sütunun yarısını aşmaz
           2 görsel  → yan yana iki kare, satırı doldurur
           3+ görsel → kaydırılabilir ray
         ─────────────────────────────────────────────────────────────────── --}}
    @if ($gallery)
        @php $adet = count($gallery); @endphp

        <section class="surface-paper pb-[clamp(3rem,6vw,5rem)]">
            @if ($adet <= 2)
                <div class="shell">
                    {{-- Genişlik sınırlı: iki kare tam satırı kaplayınca her biri
                         700 pikseli aşıyor ve sayfanın en ağır bloğu oluyordu. --}}
                    <div class="grid gap-[clamp(1rem,2vw,2rem)] {{ $adet === 2 ? 'sm:grid-cols-2 lg:max-w-[76%]' : 'sm:max-w-[46%]' }}"
                         data-reveal-stagger="0.08">
                        @foreach ($gallery as $index => $image)
                            <div class="shot ar-square" data-veil>
                                <img src="{{ $image }}"
                                     alt="{{ $brand->name }}"
                                     loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="shell" data-rail-group>
                    <div class="flex items-center justify-end gap-2">
                        <button type="button" class="rail-btn" data-rail-prev
                                aria-label="{{ __('site.common.previous') }}">
                            <svg viewBox="0 0 16 16" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                                <path d="M14 8H3M7 3L2 8l5 5"/>
                            </svg>
                        </button>

                        <button type="button" class="rail-btn" data-rail-next
                                aria-label="{{ __('site.common.next') }}">
                            <svg viewBox="0 0 16 16" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                                <path d="M2 8h11M9 3l5 5-5 5"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Yüklenen görsellerin tamamı kare; 4:3 çerçeve tepeden
                         ve alttan dörtte birini kırpıyordu. --}}
                    <div class="rail rail-wide mt-6" data-rail data-reveal-stagger="0.06">
                        @foreach ($gallery as $index => $image)
                            <div class="shot ar-square" data-veil>
                                <img src="{{ $image }}"
                                     alt="{{ $brand->name }}"
                                     loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
    @endif

    {{-- ═══ MARKANIN ÜRÜNLERİ ═══════════════════════════════════════════════
         Bölüm yalnızca ürün VARSA basılır. Boş bir çerçeve göstermek yerine
         aşağıdaki şube daveti işi devralıyor.
         ─────────────────────────────────────────────────────────────────── --}}
    @if ($products->isNotEmpty())
        <section class="surface-paper pb-[clamp(4rem,8vw,7rem)]">
            <div class="shell">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <h2 class="h2" data-split>{{ __('site.brand.products') }}</h2>

                    @if ($productCount > count($products))
                        <a href="{{ Locale::route('shop.index') }}?marka={{ $brand->slug }}"
                           class="more" data-reveal="fade">{{ __('site.brand.all_products') }}</a>
                    @endif
                </div>

                <div class="mt-[clamp(2rem,4vw,3rem)] grid gap-x-[clamp(1rem,2vw,2rem)] gap-y-[clamp(2.5rem,4vw,3.5rem)] sm:grid-cols-2 lg:grid-cols-4"
                     data-reveal-stagger="0.07">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ═══ ŞUBE DAVETİ ═════════════════════════════════════════════════════
         Markaların çoğunun kataloğu henüz sitede değil. "Ürün bulunamadı"
         demek yerine işin gerçeğini söylüyoruz: koleksiyon mağazada. Yanına
         gerçek şubeler ve iki iletişim yolu konuyor.
         ─────────────────────────────────────────────────────────────────── --}}
    <section class="surface-ink-2 band-tight">
        <div class="shell">
            <div class="grid12 items-start gap-y-10">

                <div class="col-span-12 lg:col-span-5">
                    <h2 class="h2" data-split>{{ __('site.brand.showroom_title') }}</h2>
                    <p class="body-m mt-5" data-reveal>{{ __('site.brand.showroom_body') }}</p>

                    <div class="mt-8 flex flex-wrap gap-3" data-reveal>
                        <a href="{{ Locale::route('contact') }}?marka={{ $brand->slug }}" class="btn btn-line">
                            {{ __('site.nav.contact') }}
                        </a>

                        @if ($whatsapp)
                            <a href="https://wa.me/{{ $whatsapp }}?text={{ rawurlencode($brand->name) }}"
                               class="btn btn-line"
                               target="_blank"
                               rel="noopener"
                               data-no-veil>
                                {{ __('site.brand.whatsapp') }}
                            </a>
                        @endif
                    </div>
                </div>

                @if ($branches->isNotEmpty())
                    <div class="col-span-12 lg:col-span-6 lg:col-start-7">
                        <dl class="list" data-reveal-stagger="0.06">
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
        </div>
    </section>

    {{-- ═══ DİĞER MARKALAR ══════════════════════════════════════════════════ --}}
    @if ($others->isNotEmpty())
        {{-- Beyaz zemin şart: .brand-cell'in kendisi paper-2, aynı zeminde
             görünmez kalırdı. --}}
        <section class="surface-paper band-tight">
            <div class="shell">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <h2 class="h3" data-split>{{ __('site.brand.others') }}</h2>
                    <a href="{{ Locale::route('brands') }}" class="more" data-reveal="fade">{{ __('site.common.view_all') }}</a>
                </div>

                <div class="brand-wall mt-[clamp(2rem,4vw,3rem)]" data-reveal-stagger="0.05">
                    @foreach ($others as $other)
                        <a href="{{ Locale::route('brands.show', $other) }}"
                           class="brand-cell"
                           aria-label="{{ $other->name }}">
                            @if ($logo = $other->logoUrl())
                                <img src="{{ $logo }}" alt="{{ $other->name }}" loading="lazy">
                            @else
                                <span class="brand-name">{{ $other->name }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</x-layout>
