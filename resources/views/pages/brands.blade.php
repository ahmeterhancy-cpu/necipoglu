@use('App\Support\Locale')

<x-layout :title="__('site.nav.brands')">

    <x-page-hero :eyebrow="__('site.home.brands_eyebrow')"
                 :title="__('site.home.brands_title')" />

    <section class="surface-paper pb-[clamp(5rem,12vh,10rem)]">
        <div class="shell">
            @if ($brands->isEmpty())
                <x-empty-state />
            @else
                <div class="grid gap-x-[clamp(1rem,2vw,2rem)] gap-y-[clamp(2rem,4vw,3rem)] sm:grid-cols-2 lg:grid-cols-3"
                     data-reveal-stagger="0.06">
                    @foreach ($brands as $brand)
                        {{-- Kart artik disariya degil markanin kendi sayfasina
                             gidiyor; dis site baglantisi orada duruyor. --}}
                        <a href="{{ Locale::route('brands.show', $brand) }}"
                           class="group flex flex-col items-center justify-center rounded-md border border-line p-[clamp(1.5rem,3vw,2.5rem)] transition-colors duration-500 hover:border-ink"
                           data-cursor="{{ __('site.common.view') }}">

                            <div class="flex h-20 items-center">
                                @if ($logo = $brand->logoUrl())
                                    <img src="{{ $logo }}"
                                         alt="{{ $brand->name }}"
                                         loading="lazy"
                                         class="max-h-14 max-w-full w-auto object-contain">
                                @else
                                    <span class="h4 opacity-70 transition-opacity duration-500 group-hover:opacity-100">{{ $brand->name }}</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

</x-layout>
