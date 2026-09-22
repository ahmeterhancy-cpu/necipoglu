@use('App\Support\Locale')
@use('App\Models\Branch')
@use('App\Models\Setting')

@php
    $branches = Branch::query()->active()->ordered()->get();
    $tr = app()->getLocale() === 'tr';
@endphp

<x-layout>

    {{-- ═══ HERO ════════════════════════════════════════════════════════════
         Referanslardaki gibi tam genişlik fotoğraf, altta az yazı ve tek
         bağlantı. Fark: görsel kaydırmayla usulca kayıyor, başlık satır
         satır yükseliyor.
         ─────────────────────────────────────────────────────────────────── --}}
    <section class="relative flex min-h-[92svh] items-end overflow-hidden surface-ink" data-topbar-overlay>
        <div class="absolute inset-0 overflow-hidden">
            @if ($hero = Setting::get('hero_image'))
                <img src="{{ Storage::url($hero) }}" alt="" class="h-full w-full object-cover" data-parallax="0.1" fetchpriority="high">
            @else
                <div class="placeholder h-full w-full" data-label="{{ $tr ? 'Showroom görseli' : 'Showroom image' }}"></div>
            @endif

            {{-- Okunabilirlik katmanı: alttan yukarı koyulaşan perde + sol
                 tarafta yatay bir perde daha. Metin hangi fotoğrafın üstüne
                 denk gelirse gelsin kontrast garanti altında. --}}
            <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(30,34,36,.92) 0%, rgba(30,34,36,.55) 38%, rgba(30,34,36,.15) 72%, rgba(30,34,36,.30) 100%)"></div>
            <div class="absolute inset-0 hidden lg:block" style="background:linear-gradient(to right, rgba(30,34,36,.62) 0%, rgba(30,34,36,.18) 46%, transparent 68%)"></div>
        </div>

        {{-- Tüm metin sol alt köşede tek sütunda — referans sitelerin kalıbı
             ve kontrast açısından en güvenli yerleşim. --}}
        <div class="shell relative w-full pb-[clamp(3rem,7vw,6rem)] pt-[calc(var(--topbar)+4rem)]">
            <div class="max-w-[46rem]">
                <p class="eyebrow" data-reveal="fade">{{ Setting::get('hero_eyebrow') }}</p>
                <h1 class="h1 mt-6" data-split>{{ Setting::get('hero_title') }}</h1>
                <p class="lede mt-7" style="color:rgba(241,238,231,.86)" data-reveal>{{ Setting::get('hero_lede') }}</p>

                <div class="mt-9 flex flex-wrap gap-4" data-reveal>
                    <a href="{{ Locale::route('shop.index') }}" class="btn" data-magnetic="0.2">
                        {{ __('site.shop.title') }}
                        <svg viewBox="0 0 16 16" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path d="M2 8h11M9 4l4 4-4 4"/>
                        </svg>
                    </a>
                    <a href="{{ Locale::route('contact') }}" class="btn btn-line">{{ __('site.nav.contact') }}</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ KATEGORİ IZGARASI ═══════════════════════════════════════════════
         Referanslarda hero'nun hemen altında duran kalıp. Kare görsel,
         altında isim. Hover'da görsel çok hafif yaklaşır.
         ─────────────────────────────────────────────────────────────────── --}}
    {{-- Bölüm ekrana kilitlenir; aşağı kaydırdıkça kartlar yana kayar.
         Dokunmatikte pin kapanır, parmakla yana kaydırmaya döner. --}}
    <section class="surface-paper-2 pin" data-pin data-rail-group>
      <div class="pin-stage band-tight">
        <div class="shell">
            {{-- Bölüm başlığı sade tutuldu: üstünde ayrıca etiket yok, çünkü
                 "Ürün Grupları" zaten bölümün ne olduğunu söylüyor. --}}
            <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-5">
                <h2 class="h2" data-split>{{ __('site.home.range_title') }}</h2>

                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <button type="button" class="rail-btn" data-rail-prev
                                aria-label="{{ $tr ? 'Önceki' : 'Previous' }}">
                            <svg viewBox="0 0 16 16" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                                <path d="M14 8H3M7 3L2 8l5 5"/>
                            </svg>
                        </button>

                        <button type="button" class="rail-btn" data-rail-next
                                aria-label="{{ $tr ? 'Sonraki' : 'Next' }}">
                            <svg viewBox="0 0 16 16" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                                <path d="M2 8h11M9 3l5 5-5 5"/>
                            </svg>
                        </button>
                    </div>

                    <a href="{{ Locale::route('shop.index') }}" class="more" data-reveal="fade">{{ __('site.common.view_all') }}</a>
                </div>
            </div>
        </div>

        {{-- Tek sıra ray. Altı kart ekrana sığar; fazlası sağa taşar ve
             ray sola kaydırılır. Kabuğun dışında duruyor ki son kart
             ekranın sağ kenarına taşabilsin — kaydırılabilirliğin işareti. --}}
        <div class="rail" data-rail data-reveal-stagger="0.06">
            @foreach ($categories as $category)
                <a href="{{ Locale::route('shop.category', $category) }}"
                   class="cat-card"
                   data-veil
                   data-cursor="{{ __('site.common.view') }}"
                   aria-label="{{ $category->name }} — {{ $category->tagline }}">

                    @if ($category->thumbnail)
                        <img src="{{ Storage::url($category->thumbnail) }}" alt="" loading="lazy" draggable="false">
                    @else
                        <span class="placeholder veil-inner block h-full w-full" data-label="{{ $category->name }}"></span>
                    @endif

                    <span class="cat-card-label">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>

        <div class="shell">
            <div class="rail-track">
                <span class="rail-thumb" data-rail-thumb></span>
            </div>
        </div>
      </div>
    </section>

    {{-- ═══ MARKALAR ════════════════════════════════════════════════════════
         Temsil edilen üreticiler. Hücreler saç teli ızgarada dizilir;
         logo yüklenmemişse marka adı tipografiyle durur, logo gelince
         kendiliğinden onun yerini alır.
         ─────────────────────────────────────────────────────────────────── --}}
    @if ($brands->isNotEmpty())
        <section class="surface-paper band">
            <div class="shell">
                <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-4">
                    <h2 class="h2" data-split>{{ __('site.nav.brands') }}</h2>
                    <a href="{{ Locale::route('brands') }}" class="more" data-reveal="fade">{{ __('site.common.view_all') }}</a>
                </div>

                <p class="lede mt-5" data-reveal>{{ __('site.home.brands_title') }}</p>

                <div class="brand-wall mt-[clamp(2.5rem,5vw,4rem)]" data-reveal-stagger="0.05">
                    @foreach ($brands as $brand)
                        <a href="{{ Locale::route('brands.show', $brand) }}"
                           class="brand-cell"
                           aria-label="{{ $brand->name }}">

                            @if ($logo = $brand->logoUrl())
                                <img src="{{ $logo }}" alt="{{ $brand->name }}" loading="lazy">
                            @else
                                <span class="brand-name">{{ $brand->name }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ═══ MEKÂN ŞERİDİ ════════════════════════════════════════════════════
         Tam genişlik uygulama fotoğrafı. Referanslardaki koleksiyon
         banner'ının karşılığı; parallax ile derinlik kazanıyor.
         ─────────────────────────────────────────────────────────────────── --}}
    <section class="relative overflow-hidden surface-ink">
        <div class="absolute inset-0 overflow-hidden">
            @if ($scene = Setting::get('scene_image'))
                <img src="{{ Storage::url($scene) }}" alt="" class="h-full w-full object-cover" loading="lazy" data-parallax="0.14">
            @else
                <div class="placeholder h-full w-full" data-label="{{ $tr ? 'Uygulama fotoğrafı' : 'Application photo' }}"></div>
            @endif
            <div class="absolute inset-0 bg-ink-2/62"></div>
        </div>

        <div class="shell relative py-[clamp(5rem,14vw,12rem)]">
            <div class="max-w-[46rem]">
                <p class="eyebrow" data-reveal="fade">{{ $tr ? 'Kuruluş ' . Setting::get('founded_year') : 'Established ' . Setting::get('founded_year') }}</p>
                <h2 class="h2 mt-6" data-split>{{ Setting::get('manifesto_title') }}</h2>
                <p class="lede mt-7" data-reveal>{{ Setting::get('manifesto_body') }}</p>

                <a href="{{ Locale::route('about') }}" class="btn btn-line mt-9" data-reveal>{{ __('site.nav.about') }}</a>
            </div>
        </div>
    </section>

    {{-- ═══ ÖNE ÇIKAN ÜRÜNLER ═══════════════════════════════════════════════ --}}
    @if ($featuredProducts->isNotEmpty())
        <section class="surface-paper-2 band">
            <div class="shell">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <div>
                        <p class="eyebrow" data-reveal="fade">{{ $tr ? 'Seçilenler' : 'Selected' }}</p>
                        <h2 class="h2 mt-5" data-split>{{ __('site.shop.title') }}</h2>
                    </div>

                    <a href="{{ Locale::route('shop.index') }}" class="more" data-reveal="fade">{{ __('site.common.view_all') }}</a>
                </div>

                <div class="mt-[clamp(2.5rem,5vw,4rem)] grid gap-x-[clamp(1rem,2vw,2rem)] gap-y-[clamp(2.5rem,4vw,3.5rem)] sm:grid-cols-2 lg:grid-cols-4"
                     data-reveal-stagger="0.07">
                    @foreach ($featuredProducts as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ═══ REFERANSLAR ═════════════════════════════════════════════════════ --}}
    <section class="surface-paper band">
        <div class="shell">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div>
                    <p class="eyebrow" data-reveal="fade">{{ __('site.home.projects_eyebrow') }}</p>
                    <h2 class="h2 mt-5" data-split>{{ __('site.home.projects_title') }}</h2>
                </div>

                @if ($projects->isNotEmpty())
                    <a href="{{ Locale::route('projects.index') }}" class="more" data-reveal="fade">{{ __('site.common.view_all') }}</a>
                @endif
            </div>

            @if ($projects->isEmpty())
                <x-empty-state class="mt-10" />
            @else
                {{-- Dört proje TEK SIRADA. Daha once 2x2 dizilip ekranin tamamini
                     kapliyordu; ana sayfa bir vitrin, dosya degil — tam liste
                     Referanslar sayfasinda. Dikey oran, ustteki kare urun
                     izgarasindan ayirir. --}}
                <div class="mt-[clamp(2rem,4vw,3rem)] grid gap-x-[clamp(1rem,2vw,2rem)] gap-y-[clamp(2rem,3vw,2.5rem)] sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($projects as $project)
                        <a href="{{ Locale::route('projects.show', $project) }}"
                           class="card group block"
                           data-cursor="{{ __('site.common.view') }}">

                            <div class="shot card-shot aspect-[3/2] lg:aspect-[3/4]" data-veil>
                                @if ($project->cover)
                                    <img src="{{ Storage::url($project->cover) }}" alt="{{ $project->title }}" loading="lazy">
                                @else
                                    <div class="placeholder veil-inner h-full w-full" data-label="{{ $project->title }}"></div>
                                @endif
                            </div>

                            <div class="mt-4 flex items-baseline justify-between gap-4">
                                <h3 class="h4 card-title">{{ $project->title }}</h3>
                                <span class="eyebrow eyebrow-mute tnum shrink-0">{{ $project->year }}</span>
                            </div>

                            <p class="body-s mute mt-1.5">{{ $project->location }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ═══ YAPI MARKET ═════════════════════════════════════════════════════
         Grubun ikinci perakende kolu. İçerik ayarlardan; başlık boşsa
         şerit hiç basılmaz — yarım bir tanıtım göstermenin anlamı yok.
         ─────────────────────────────────────────────────────────────────── --}}
    @php
        $marketBaslik = Setting::get('market_title');
        $marketTanitim = Setting::get('market_body');
        $marketGorsel = Setting::get('market_image');
    @endphp

    @if ($marketBaslik)
        <section class="relative overflow-hidden surface-ink">
            @if ($marketGorsel)
                <div class="absolute inset-0" data-parallax="0.1">
                    <img src="{{ Storage::url($marketGorsel) }}"
                         alt=""
                         class="h-[118%] w-full object-cover"
                         loading="lazy"
                         aria-hidden="true">

                    <div class="absolute inset-0 bg-ink-2/66"></div>
                </div>
            @endif

            <div class="shell relative py-[clamp(4rem,10vw,8rem)]">
                <div class="max-w-[42rem]">
                    <p class="eyebrow" data-reveal="fade">{{ __('site.nav.market') }}</p>
                    <h2 class="h2 mt-6" data-split>{{ $marketBaslik }}</h2>

                    @if ($marketTanitim)
                        <p class="lede mt-7" data-reveal>{{ $marketTanitim }}</p>
                    @endif

                    <a href="{{ Locale::route('market') }}" class="btn btn-line mt-9" data-reveal>
                        {{ __('site.common.view') }}
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ═══ ŞUBELER ═════════════════════════════════════════════════════════ --}}
    <section class="surface-ink band">
        <div class="shell">
            <div class="grid12 items-end gap-y-8">
                <div class="col-span-12 lg:col-span-7">
                    <p class="eyebrow" data-reveal="fade">{{ __('site.home.branches_eyebrow') }}</p>
                    <h2 class="h2 mt-5 max-w-[18ch]" data-split>{{ __('site.home.branches_title') }}</h2>
                </div>

                <div class="col-span-12 flex lg:col-span-4 lg:col-start-9 lg:justify-end">
                    <a href="{{ Locale::route('contact') }}" class="btn btn-line" data-reveal="fade">{{ __('site.nav.contact') }}</a>
                </div>
            </div>

            <div class="mt-[clamp(2.5rem,5vw,4rem)] grid gap-x-[clamp(1rem,2vw,2.5rem)] gap-y-10 md:grid-cols-3"
                 data-reveal-stagger="0.08">
                @foreach ($branches as $branch)
                    <div class="border-t pt-7" style="border-color:rgba(245,243,239,.18)">
                        <p class="eyebrow eyebrow-mute">{{ $branch->kind }}</p>
                        <h3 class="h3 mt-4">{{ $branch->name }}</h3>
                        <p class="body-s mt-3">{{ $branch->address }}</p>

                        <p class="mt-4 flex flex-wrap gap-x-5 gap-y-1">
                            @foreach ($branch->phones ?? [] as $phone)
                                <a href="tel:{{ $branch->dialable($phone) }}" data-no-veil class="body-s link tnum">{{ $phone }}</a>
                            @endforeach
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</x-layout>
