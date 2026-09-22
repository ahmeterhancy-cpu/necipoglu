@use('App\Support\Locale')
@use('App\Models\Setting')

@php
    $tr = app()->getLocale() === 'tr';
    $kurulus = (int) Setting::get('founded_year', 1985);
@endphp

<x-layout :title="__('site.nav.about')">

    {{-- Hakkımızda'nın KENDİ metni var; boşsa ana sayfadaki kurumsal
         metne düşer. Önceden ikisi de aynı metni basıyordu. --}}
    <x-page-hero :eyebrow="__('site.nav.about')"
                 :title="Setting::get('about_title') ?: Setting::get('manifesto_title')"
                 :lede="Setting::get('about_body') ?: Setting::get('manifesto_body')" />

    {{-- ═══ SAYILAR ═════════════════════════════════════════════════════════
         Beşi de gerçek veriden: kuruluş yılı ayarlardan, şube ve marka
         sayısı kayıtlardan, ürün grubu kategori sayısından, deneyim
         hesaplanarak. Elle yazılmış rakam yok — kayıt değişince değişir.
         ─────────────────────────────────────────────────────────────────── --}}
    <section class="surface-paper pb-[clamp(3rem,6vw,5rem)]">
        <div class="shell">
            <div class="grid grid-cols-2 gap-x-6 gap-y-9 sm:grid-cols-3 lg:grid-cols-5"
                 data-reveal-stagger="0.08">
                @foreach ([
                    [$kurulus, __('site.common.since'), '1.9'],
                    [(int) date('Y') - $kurulus, $tr ? 'Yıllık deneyim' : 'Years of experience', null],
                    [$branches->count(), __('site.common.branches'), null],
                    [$brands->count(), __('site.nav.brands'), null],
                    [$categories->count(), __('site.common.categories'), null],
                ] as [$sayi, $etiket, $sure])
                    <div>
                        <p class="h3 tnum">
                            <span data-count="{{ $sayi }}" @if ($sure) data-count-duration="{{ $sure }}" @endif>0</span>
                        </p>
                        <p class="eyebrow eyebrow-mute mt-3">{{ $etiket }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══ MEKÂN ŞERİDİ ════════════════════════════════════════════════════
         Sayfa baştan sona tipografiydi, gözün dinlendiği yer yoktu.
         Tam genişlik fotoğraf; parallax ile derinlik kazanıyor.
         ─────────────────────────────────────────────────────────────────── --}}
    <section class="relative overflow-hidden surface-ink">
        <div class="absolute inset-0" data-parallax="0.12">
            {{-- Panelde alan boşaltılırsa ayar boş DİZE döner, null değil;
                 Storage::url('') kırık bir adres uretirdi. --}}
            @php $seritGorsel = Setting::get('about_image') ?: 'media/scene.jpg'; @endphp

            <img src="{{ Storage::url($seritGorsel) }}"
                 alt=""
                 class="h-[118%] w-full object-cover"
                 loading="lazy"
                 aria-hidden="true">

            <div class="absolute inset-0 bg-ink-2/68"></div>
        </div>

        <div class="shell relative py-[clamp(4rem,10vw,8rem)]">
            <div class="max-w-[42rem]">
                <p class="eyebrow" data-reveal="fade">{{ $tr ? 'Kuruluş '.$kurulus : 'Established '.$kurulus }}</p>

                <h2 class="h2 mt-6" data-split>
                    {{ $tr
                        ? 'Malzeme, sonradan düzeltilmesi en zor karardır.'
                        : 'Material is the hardest decision to undo.' }}
                </h2>

                <p class="lede mt-7" data-reveal>
                    {{ $tr
                        ? 'Duvara çıkan seramik yirmi yıl orada kalır; yanlış seçilen bir zemin sökülüp yeniden yapılır. Bu yüzden satıştan önce mekânı, kullanım yoğunluğunu ve bütçeyi konuşuruz. Hangi ürünün nerede işe yaradığını kırk yılda öğrendik.'
                        : 'Tile that goes on a wall stays there for twenty years; a wrongly chosen floor has to be lifted and laid again. So before the sale we talk about the space, how hard it will be used and the budget. Four decades taught us which product works where.' }}
                </p>
            </div>
        </div>
    </section>

    {{-- ═══ VİZYON VE MİSYON ════════════════════════════════════════════════
         Metin panelden düzenlenir; boş bırakılan sütun hiç basılmaz.
         ─────────────────────────────────────────────────────────────────── --}}
    @php
        $vizyon = Setting::get('vision_body');
        $misyon = Setting::get('mission_body');
    @endphp

    @if ($vizyon || $misyon)
        <section class="surface-paper band-tight">
            <div class="shell">
                <div class="grid12 gap-y-10">
                    @if ($vizyon)
                        <div class="col-span-12 md:col-span-6 lg:col-span-5">
                            <p class="eyebrow" data-reveal="fade">{{ $tr ? 'Vizyon' : 'Vision' }}</p>
                            <p class="body-m mt-5" data-reveal>{{ $vizyon }}</p>
                        </div>
                    @endif

                    @if ($misyon)
                        <div class="col-span-12 md:col-span-6 lg:col-span-5 lg:col-start-8">
                            <p class="eyebrow" data-reveal="fade">{{ $tr ? 'Misyon' : 'Mission' }}</p>
                            <p class="body-m mt-5" data-reveal>{{ $misyon }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ═══ NASIL ÇALIŞIYORUZ ═══════════════════════════════════════════════
         Zemin taş: vizyon/değerler ile kitleler arasında ayrım olsun.
         Dört bölüm ard arda beyazdı, sayfa tek parça görünüyordu.
         ─────────────────────────────────────────────────────────────────── --}}
    <section class="surface-paper-2 band-tight">
        <div class="shell">
            <div class="grid12 gap-y-10">
                <div class="col-span-12 lg:col-span-4">
                    <p class="eyebrow" data-reveal="fade">{{ $tr ? 'Nasıl çalışıyoruz' : 'How we work' }}</p>
                    <h2 class="h2 mt-5" data-split>
                        {{ $tr ? 'Malzemeyi satmadan önce projeyi anlarız.' : 'We understand the project before we sell the material.' }}
                    </h2>
                </div>

                {{-- Adımlar numaralı: bu bir süreç, düz liste değil. --}}
                <div class="col-span-12 lg:col-span-7 lg:col-start-6">
                    @foreach ([
                        ['tr' => ['Keşif ve ölçü', 'Şantiyede veya evde yerinde ölçü alır, zemin ve duvar durumunu değerlendiririz.'], 'en' => ['Survey and measurement', 'We measure on site and assess the condition of floors and walls.']],
                        ['tr' => ['Malzeme seçimi', 'Kullanım yoğunluğuna, ıslak hacme ve bütçeye uygun ürünleri birlikte belirleriz.'], 'en' => ['Material selection', 'Together we choose products suited to traffic, wet areas and budget.']],
                        ['tr' => ['Tedarik ve teslim', 'Stok durumunu şeffaf paylaşır, üç şubemizden hızlı teslimat sağlarız.'], 'en' => ['Supply and delivery', 'We share stock status transparently and deliver quickly from three branches.']],
                        ['tr' => ['Uygulama desteği', 'Ustaya teknik bilgi, derz ve yapıştırıcı önerisi, bakım talimatı veririz.'], 'en' => ['Installation support', 'We provide technical guidance, grout and adhesive recommendations and care instructions.']],
                    ] as $i => $step)
                        @php $s = $step[app()->getLocale()] ?? $step['tr']; @endphp

                        <div class="flex gap-6 border-t border-line py-6 @if ($loop->last) border-b @endif" data-reveal="fade">
                            <span class="eyebrow eyebrow-mute tnum shrink-0 pt-1">{{ sprintf('%02d', $i + 1) }}</span>

                            <span>
                                <span class="h4 block">{{ $s[0] }}</span>
                                <span class="body-s mute mt-2 block max-w-[54ch]">{{ $s[1] }}</span>
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ KİMLERE HİZMET VERİYORUZ ════════════════════════════════════════
         Aynı raf üç farklı ihtiyaca bakıyor; her birine ne verdiğimizi
         ayrı ayrı söylemek "herkese hizmet veriyoruz" demekten iyidir.
         ─────────────────────────────────────────────────────────────────── --}}
    <section class="surface-paper band-tight">
        <div class="shell">
            <div class="grid12 gap-y-10">
                <div class="col-span-12 lg:col-span-4">
                    <p class="eyebrow" data-reveal="fade">{{ $tr ? 'Kimlerle çalışıyoruz' : 'Who we work with' }}</p>
                    <h2 class="h2 mt-5" data-split>
                        {{ $tr ? 'Aynı raf, farklı ihtiyaçlar.' : 'One shelf, different needs.' }}
                    </h2>
                </div>

                <div class="col-span-12 lg:col-span-7 lg:col-start-6">
                    {{-- Kart ızgarası: bunlar da sıra değil, dört ayrı muhatap.
                         Üstteki süreç listesiyle aynı görünmesin. --}}
                    <div class="grid gap-x-[clamp(1.5rem,3vw,2.5rem)] gap-y-8 sm:grid-cols-2">
                    @foreach ([
                        ['tr' => ['Müteahhit ve yatırımcı', 'Blok bazında fiyat, parti tutarlılığı ve etaplı teslim. Şantiye programına göre sevkiyat planlarız.'], 'en' => ['Contractors and developers', 'Block pricing, batch consistency and phased delivery, scheduled to the site programme.']],
                        ['tr' => ['Mimar ve iç mimar', 'Numune, teknik föy ve ebat seçenekleri. Projeye özel malzeme paftası hazırlarız.'], 'en' => ['Architects and interior designers', 'Samples, technical sheets and size options, with a project-specific material schedule.']],
                        ['tr' => ['Ev sahibi', 'Showroom’da birebir görme, bütçeye göre alternatif ve usta yönlendirmesi.'], 'en' => ['Homeowners', 'Seeing the material in the showroom, alternatives to suit the budget and guidance on installers.']],
                        ['tr' => ['Otel ve ticari proje', 'Yoğun kullanıma dayanıklı sınıflar, yangın ve kayma direnci belgeleri, tekrar sipariş güvencesi.'], 'en' => ['Hotels and commercial projects', 'Classes rated for heavy traffic, fire and slip resistance documentation, and repeat-order continuity.']],
                    ] as $i => $kitle)
                        @php $k = $kitle[app()->getLocale()] ?? $kitle['tr']; @endphp

                        <div class="border-t border-line pt-5" data-reveal="fade">
                            <p class="h4">{{ $k[0] }}</p>
                            <p class="body-s mute mt-3">{{ $k[1] }}</p>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ ÜRÜN GRUPLARI ═══════════════════════════════════════════════════
         Ne sattığımızı anlatmak yerine göstermek. Kartlar mağazaya bağlanır.
         ─────────────────────────────────────────────────────────────────── --}}
    @if ($categories->isNotEmpty())
        <section class="surface-paper-2 band-tight">
            <div class="shell">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <h2 class="h2" data-split>{{ __('site.home.range_title') }}</h2>
                    <a href="{{ Locale::route('shop.index') }}" class="more" data-reveal="fade">{{ __('site.common.view_all') }}</a>
                </div>

                <div class="mt-[clamp(2rem,4vw,3rem)] grid grid-cols-2 gap-[clamp(.75rem,1.6vw,1.5rem)] sm:grid-cols-3 lg:grid-cols-6"
                     data-reveal-stagger="0.06">
                    @foreach ($categories as $category)
                        <a href="{{ Locale::route('shop.category', $category) }}"
                           class="cat-card cat-card-square"
                           data-cursor="{{ __('site.common.view') }}"
                           aria-label="{{ $category->name }}">
                            @if ($category->thumbnail)
                                <img src="{{ Storage::url($category->thumbnail) }}" alt="{{ $category->name }}" loading="lazy">
                            @else
                                <div class="placeholder h-full w-full"></div>
                            @endif

                            <span class="cat-card-label">{{ $category->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ═══ MARKALAR ════════════════════════════════════════════════════════
         Kim olduğumuzun en sağlam kanıtı temsil ettiğimiz üreticiler.
         Beyaz zemin şart: .brand-cell'in kendisi paper-2.
         ─────────────────────────────────────────────────────────────────── --}}
    @if ($brands->isNotEmpty())
        <section class="surface-paper band-tight">
            <div class="shell">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <div>
                        <p class="eyebrow" data-reveal="fade">{{ __('site.home.brands_eyebrow') }}</p>
                        <h2 class="h2 mt-5" data-split>{{ __('site.home.brands_title') }}</h2>
                    </div>

                    <a href="{{ Locale::route('brands') }}" class="more" data-reveal="fade">{{ __('site.common.view_all') }}</a>
                </div>

                <div class="brand-wall mt-[clamp(2rem,4vw,3rem)]" data-reveal-stagger="0.04">
                    @foreach ($brands->take(18) as $brand)
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

    {{-- ═══ ŞUBELER ═════════════════════════════════════════════════════════ --}}
    @if ($branches->isNotEmpty())
        <section class="surface-ink-2 band-tight">
            <div class="shell">
                <div class="grid12 items-start gap-y-10">
                    <div class="col-span-12 lg:col-span-4">
                        <p class="eyebrow" data-reveal="fade">{{ __('site.common.branches') }}</p>
                        <h2 class="h2 mt-5" data-split>
                            {{ $tr ? 'Üç noktada, aynı standart.' : 'Three locations, one standard.' }}
                        </h2>
                    </div>

                    <div class="col-span-12 lg:col-span-7 lg:col-start-6">
                        <dl class="list" data-reveal-stagger="0.06">
                            @foreach ($branches as $branch)
                                <div class="list-row">
                                    <span>
                                        <dt class="list-title block">
                                            {{ $branch->name }}
                                            @if ($branch->kind)<span class="list-meta"> · {{ $branch->kind }}</span>@endif
                                        </dt>
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
                </div>
            </div>
        </section>
    @endif

    {{-- ═══ İŞTİRAKLER ══════════════════════════════════════════════════════
         Yalnızca kayıt VARSA basılır. Önceden başlık ve "tümünü gör"
         düğmesi kayıt olmadan da basılıyordu; ziyaretçi boş bir sayfaya
         gidiyordu. Denetleyici $subsidiaries gönderiyordu ama görünüm
         hiç kullanmıyordu.
         ─────────────────────────────────────────────────────────────────── --}}
    @if ($subsidiaries->isNotEmpty())
        <section class="surface-paper band-tight">
            <div class="shell">
                <div class="grid12 items-end gap-y-8">
                    <div class="col-span-12 lg:col-span-7">
                        <p class="eyebrow" data-reveal="fade">{{ __('site.nav.subsidiaries') }}</p>
                        <h2 class="h2 mt-5 max-w-[14ch]" data-split>
                            {{ $tr ? 'Aynı çatı altında, farklı alanlarda.' : 'One group, several fields.' }}
                        </h2>
                    </div>

                    <div class="col-span-12 flex lg:col-span-4 lg:col-start-9 lg:justify-end">
                        <a href="{{ Locale::route('subsidiaries') }}" class="btn btn-line" data-reveal="fade">
                            {{ __('site.common.view_all') }}
                        </a>
                    </div>
                </div>

                <div class="list mt-[clamp(2rem,4vw,3rem)]" data-reveal-stagger="0.06">
                    @foreach ($subsidiaries as $subsidiary)
                        <div class="list-row">
                            <span>
                                <span class="list-title block">
                                    {{ $subsidiary->name }}
                                    @if ($subsidiary->sector)<span class="list-meta"> · {{ $subsidiary->sector }}</span>@endif
                                </span>

                                @if ($subsidiary->tagline)
                                    <span class="list-note block max-w-[62ch]">{{ $subsidiary->tagline }}</span>
                                @endif
                            </span>

                            @if ($subsidiary->founded)
                                <span class="list-meta tnum shrink-0">{{ $subsidiary->founded }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ═══ KURUMSAL KÜNYE ══════════════════════════════════════════════════
         Ticari unvan, kuruluş ve iletişim tek yerde. Tamamı ayarlardan
         ve şube kaydından geliyor; sabit yazılmış bilgi yok.
         ─────────────────────────────────────────────────────────────────── --}}
    @php
        $merkez = $branches->first();
        $eposta = Setting::get('email');
        $wa = preg_replace('/[^\d]/', '', (string) Setting::get('whatsapp'));

        $kunye = array_filter([
            [$tr ? 'Ticari unvan' : 'Registered name', Setting::get('legal_name'), null],
            [$tr ? 'Kuruluş' : 'Established', $kurulus, null],
            [$tr ? 'Merkez' : 'Head office', $merkez?->address, null],
            [$tr ? 'E-posta' : 'Email', $eposta, $eposta ? 'mailto:'.$eposta : null],
            ['WhatsApp', Setting::get('whatsapp'), $wa ? 'https://wa.me/'.$wa : null],
        ], fn ($satir) => filled($satir[1]));
    @endphp

    {{-- Künye ve kapanış TEK bölüm: ikisi de kısa, ayrı bölüm olunca
         aralarında gereksiz bir boşluk kalıyordu. Künye beş satır; üç
         sütunda son hücre boş kalıyordu, iki sütuna alındı. --}}
    <section class="surface-paper band-tight">
        <div class="shell">
            @if ($kunye)
                <p class="eyebrow" data-reveal="fade">{{ $tr ? 'Kurumsal künye' : 'Company details' }}</p>

                <dl class="mt-8 grid gap-x-[clamp(2rem,4vw,4rem)] gap-y-7 sm:grid-cols-2"
                    data-reveal-stagger="0.06">
                    @foreach ($kunye as [$etiket, $deger, $baglanti])
                        <div class="flex flex-wrap items-baseline justify-between gap-x-6 gap-y-1 border-t border-line pt-4">
                            <dt class="eyebrow eyebrow-mute">{{ $etiket }}</dt>
                            <dd class="body-s text-right">
                                @if ($baglanti)
                                    <a href="{{ $baglanti }}" class="link" @if (str_starts_with($baglanti, 'http')) target="_blank" rel="noopener" data-no-veil @endif>{{ $deger }}</a>
                                @else
                                    {{ $deger }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>

                <hr class="rule mt-[clamp(3rem,6vw,4.5rem)]" data-draw>
            @endif
        </div>

        <div class="shell-narrow mt-[clamp(3rem,6vw,4.5rem)] text-center">
            <h2 class="h2 mx-auto max-w-[16ch]" data-split>{{ __('site.home.cta_title') }}</h2>
            <p class="lede mute mx-auto mt-7" data-reveal>{{ __('site.home.cta_body') }}</p>

            <div class="mt-9 flex justify-center" data-reveal>
                <a href="{{ Locale::route('contact') }}" class="btn" data-magnetic="0.24">{{ __('site.nav.contact') }}</a>
            </div>
        </div>
    </section>

</x-layout>
