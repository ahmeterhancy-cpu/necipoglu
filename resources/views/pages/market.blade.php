@use('App\Support\Locale')
@use('App\Models\Setting')

@php
    $tr = app()->getLocale() === 'tr';

    $baslik = Setting::get('market_title');
    $tanitim = Setting::get('market_body');
    $uzun = Setting::get('market_intro');

    /* Ürün grupları panelde satır satır giriliyor; boş satırlar atılır.
       Tek kayıt için ayrı bir tablo açmak fazlaydı. */
    $gruplar = collect(preg_split('/\r\n|\r|\n/', (string) Setting::get('market_groups')))
        ->map(fn ($satir) => trim($satir))
        ->filter()
        ->values();

    $adres = Setting::get('market_address');
    $telefon = Setting::get('market_phone');
    $saatler = Setting::get('market_hours');
    $gorsel = Setting::get('market_image');

    $whatsapp = preg_replace('/[^\d]/', '', (string) Setting::get('whatsapp', config('site.company.whatsapp')));
@endphp

<x-layout :title="__('site.nav.market')" :description="$tanitim">

    {{-- ═══ AÇILIŞ ══════════════════════════════════════════════════════════
         Düz metin açılışı üst kısmı boş bırakıyordu. Burası bir perakende
         mağazası — açılış fotoğrafla olur. Fotoğraf yoksa sade başlığa
         düşer, yani ayar girilmemişken sayfa yine de doğru görünür.
         ─────────────────────────────────────────────────────────────────── --}}
    @if ($gorsel)
        <section class="relative flex min-h-[68svh] items-end overflow-hidden surface-ink" data-topbar-overlay>
            <div class="absolute inset-0 overflow-hidden">
                <img src="{{ Storage::url($gorsel) }}"
                     alt=""
                     class="h-full w-full object-cover"
                     data-parallax="0.1"
                     fetchpriority="high"
                     aria-hidden="true">

                {{-- Okunabilirlik perdesi: metin hangi kadraja denk gelirse
                     gelsin kontrast garanti. Ana sayfadaki katmanın aynısı. --}}
                <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(30,34,36,.92) 0%, rgba(30,34,36,.55) 38%, rgba(30,34,36,.15) 72%, rgba(30,34,36,.30) 100%)"></div>
                <div class="absolute inset-0 hidden lg:block" style="background:linear-gradient(to right, rgba(30,34,36,.62) 0%, rgba(30,34,36,.18) 46%, transparent 68%)"></div>
            </div>

            <div class="shell relative w-full pb-[clamp(3rem,7vw,5rem)] pt-[calc(var(--topbar)+4rem)]">
                <div class="max-w-[44rem]">
                    <p class="eyebrow" data-reveal="fade">{{ __('site.nav.market') }}</p>
                    <h1 class="h1 mt-6" data-split>{{ $baslik ?: __('site.nav.market') }}</h1>

                    @if ($tanitim)
                        <p class="lede mt-7" style="color:rgba(241,238,231,.86)" data-reveal>{{ $tanitim }}</p>
                    @endif

                    <div class="mt-9 flex flex-wrap gap-4" data-reveal>
                        <a href="{{ Locale::route('contact') }}" class="btn" data-magnetic="0.2">
                            {{ __('site.nav.contact') }}
                            <svg viewBox="0 0 16 16" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path d="M2 8h11M9 4l4 4-4 4"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @else
        <x-page-hero :eyebrow="__('site.nav.market')"
                     :title="$baslik ?: __('site.nav.market')"
                     :lede="$tanitim" />
    @endif

    {{-- ═══ İKİ KOL ═════════════════════════════════════════════════════════
         Ziyaretçinin ilk sorusu "burası showroom'dan farklı mı?" oluyor.
         İki kolu yan yana koyup farkı söylemek, sayfayı da gövdelendiriyor.
         Karşı taraf mağazaya bağlanır — iki kol birbirini besler.
         ─────────────────────────────────────────────────────────────────── --}}
    <section class="surface-paper-2 band-tight">
        <div class="shell">
            <div class="grid gap-x-[clamp(1.5rem,3vw,3rem)] gap-y-10 md:grid-cols-2"
                 data-reveal-stagger="0.08">

                <div class="border-t border-line pt-6">
                    <p class="eyebrow eyebrow-mute">{{ $tr ? 'Showroom' : 'Showroom' }}</p>
                    <h2 class="h3 mt-4" data-split>{{ $tr ? 'Yüzey ve banyo' : 'Surfaces and bathrooms' }}</h2>
                    <p class="body-s mute mt-3 max-w-[46ch]">
                        {{ $tr
                            ? 'Seramik, mermer, parke, banyo ve armatür. Otuz üreticinin koleksiyonu üç şubede sergileniyor; ölçü ve keşifle birlikte seçiliyor.'
                            : 'Tile, marble, flooring, bathroom and fittings. Collections from thirty manufacturers on display in three branches, chosen with measurement and a site visit.' }}
                    </p>

                    <a href="{{ Locale::route('shop.index') }}" class="more mt-6">{{ __('site.shop.title') }}</a>
                </div>

                <div class="border-t border-ink pt-6">
                    <p class="eyebrow">{{ __('site.nav.market') }}</p>
                    <h2 class="h3 mt-4" data-split>{{ $tr ? 'Günlük ihtiyaç' : 'Everyday needs' }}</h2>
                    <p class="body-s mute mt-3 max-w-[46ch]">
                        {{ $tr
                            ? 'Şantiyede ve evde işi yürüten malzeme: hırdavat, boya, elektrik, tesisat. Randevu gerekmez, uğrayıp alırsınız.'
                            : 'The material that keeps a site or a home running: hardware, paint, electrics, plumbing. No appointment — you drop in and pick it up.' }}
                    </p>

                    @if ($saatler)
                        <p class="eyebrow eyebrow-mute mt-6">{{ $saatler }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ ARA SÖZ ═════════════════════════════════════════════════════════
         Fotoğraf artık açılışta kullanılıyor; buradaki cümle tek başına,
         sade bir zeminde duruyor. İki fotoğraflı bölüm üst üste gelmesin.
         ─────────────────────────────────────────────────────────────────── --}}
    @if ($uzun)
        <section class="surface-paper band-tight">
            <div class="shell">
                <p class="lede max-w-[46rem]" data-reveal>{{ $uzun }}</p>
            </div>
        </section>
    @endif

    {{-- ═══ ÜRÜN GRUPLARI ═══════════════════════════════════════════════════
         Kart ızgarası: çıplak liste satırları sayfayı boş gösteriyordu.
         ─────────────────────────────────────────────────────────────────── --}}
    @if ($gruplar->isNotEmpty())
        <section class="surface-paper band-tight">
            <div class="shell">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <div>
                        <p class="eyebrow" data-reveal="fade">{{ __('site.brand.groups') }}</p>
                        <h2 class="h2 mt-5" data-split>
                            {{ $tr ? 'Rafta ne var?' : 'What’s on the shelves?' }}
                        </h2>
                    </div>
                </div>

                <div class="mt-[clamp(2rem,4vw,3rem)] grid gap-[clamp(.75rem,1.6vw,1.5rem)] sm:grid-cols-2 lg:grid-cols-3"
                     data-reveal-stagger="0.06">
                    @foreach ($gruplar as $i => $grup)
                        <div class="tile" data-reveal="scale">
                            <p class="eyebrow eyebrow-mute tnum">{{ sprintf('%02d', $i + 1) }}</p>
                            <p class="h4 mt-4">{{ $grup }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ═══ MAĞAZADAN ═══════════════════════════════════════════════════════ --}}
    @if ($gallery)
        @php $adet = count($gallery); @endphp

        <section class="surface-paper-2 band-tight">
            @if ($adet <= 2)
                <div class="shell">
                    <div class="grid gap-[clamp(1rem,2vw,2rem)] {{ $adet === 2 ? 'sm:grid-cols-2' : 'sm:max-w-[60%]' }}"
                         data-reveal-stagger="0.08">
                        @foreach ($gallery as $image)
                            <div class="card">
                                <div class="shot card-shot ar-photo" data-veil>
                                    <img src="{{ $image }}" alt="{{ __('site.nav.market') }}" loading="lazy">
                                </div>
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

                    <div class="rail rail-wide mt-6" data-rail data-reveal-stagger="0.06">
                        @foreach ($gallery as $image)
                            <div class="card">
                                <div class="shot card-shot ar-photo" data-veil>
                                    <img src="{{ $image }}" alt="{{ __('site.nav.market') }}" loading="lazy">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
    @endif

    {{-- ═══ ZİYARET ═════════════════════════════════════════════════════════
         Şube listesi BİLEREK yok: alt bilgi zaten üç şubeyi adres ve
         telefonuyla basıyor, bu bölüm onun hemen üstünde duruyordu ve
         aynı bilgi ekranda iki kez görünüyordu.
         ─────────────────────────────────────────────────────────────────── --}}
    <section class="surface-ink-2 band-tight">
        <div class="shell">
            <div class="grid12 items-center gap-y-9">

                <div class="col-span-12 lg:col-span-6">
                    <h2 class="h2" data-split>
                        {{ $tr ? 'Uğrayın, bakalım.' : 'Come and take a look.' }}
                    </h2>

                    <p class="body-m mt-5 max-w-[44ch]" data-reveal>
                        {{ $tr
                            ? 'Aradığınızı bulamazsanız söyleyin, tedarik edelim. Miktarı büyükse fiyat konuşuruz.'
                            : 'If you cannot find what you need, tell us and we will source it. For larger quantities, we will talk price.' }}
                    </p>
                </div>

                <div class="col-span-12 lg:col-span-5 lg:col-start-8">
                    @if ($adres || $telefon)
                        <dl class="mb-8 space-y-5" data-reveal>
                            @if ($adres)
                                <div class="border-t border-line pt-4">
                                    <dt class="eyebrow eyebrow-mute">{{ $tr ? 'Adres' : 'Address' }}</dt>
                                    <dd class="body-m mt-2">{{ $adres }}</dd>
                                </div>
                            @endif

                            @if ($telefon)
                                <div class="border-t border-line pt-4">
                                    <dt class="eyebrow eyebrow-mute">{{ $tr ? 'Telefon' : 'Phone' }}</dt>
                                    <dd class="body-m mt-2 tnum">
                                        <a href="tel:{{ preg_replace('/[^\d+]/', '', $telefon) }}" class="link">{{ $telefon }}</a>
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    @endif

                    <div class="flex flex-wrap gap-3" data-reveal>
                        <a href="{{ Locale::route('contact') }}" class="btn btn-line" data-magnetic="0.2">{{ __('site.nav.contact') }}</a>

                        @if ($whatsapp)
                            <a href="https://wa.me/{{ $whatsapp }}"
                               class="btn btn-line" target="_blank" rel="noopener" data-no-veil data-magnetic="0.2">
                                {{ __('site.brand.whatsapp') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-layout>
