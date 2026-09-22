@use('App\Support\Locale')

@php
    $trail = [['name' => __('site.shop.title'), 'url' => Locale::route('shop.index')]];

    if ($category) {
        $trail[] = ['name' => $category->name, 'url' => url()->current()];
    }

    $schema = [\App\Support\Schema::breadcrumbs($trail)];
@endphp

<x-layout :title="$category?->name ?? __('site.shop.title')"
          :description="$category?->description"
          :schema="$schema">

    <x-page-hero :eyebrow="$category ? __('site.shop.title') : __('site.home.range_eyebrow')"
                 :title="$category?->name ?? __('site.shop.title')"
                 :lede="$category?->description">

        <x-slot:meta>
            {{-- Kategori filtresi — yatay kaydırılabilir şerit --}}
            <div class="-mx-[var(--gutter)] overflow-x-auto px-[var(--gutter)] pb-1" data-reveal="fade">
                <div class="flex min-w-max items-center gap-x-8">
                    <a href="{{ Locale::route('shop.index') }}"
                       class="eyebrow whitespace-nowrap transition-opacity {{ $category ? 'opacity-45 hover:opacity-100' : '' }}">
                        {{ __('site.shop.all') }}
                    </a>

                    @foreach ($categories as $item)
                        <a href="{{ Locale::route('shop.category', $item) }}"
                           class="eyebrow whitespace-nowrap transition-opacity {{ $category?->is($item) ? '' : 'opacity-45 hover:opacity-100' }}">
                            {{ $item->name }}
                            <span class="tnum opacity-50">{{ $item->products_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </x-slot:meta>
    </x-page-hero>

    <section class="surface-paper pb-[clamp(5rem,12vh,10rem)]">
        <div class="shell">

            {{-- Arama ve sıralama --}}
            <form method="get" class="flex flex-wrap items-end justify-between gap-6 pb-10" data-reveal="fade">
                <label class="min-w-0 flex-1 md:max-w-xs">
                    <span class="eyebrow eyebrow-mute block">{{ __('site.shop.search') }}</span>
                    <input type="search"
                           name="q"
                           value="{{ request('q') }}"
                           class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink-2"
                           placeholder="{{ __('site.shop.search') }}">
                </label>

                <label class="min-w-0">
                    <span class="eyebrow eyebrow-mute block">{{ __('site.shop.sort') }}</span>
                    <select name="sirala"
                            onchange="this.form.requestSubmit()"
                            class="mt-3 border-b border-line bg-transparent pb-3 pr-6 outline-none transition-colors focus:border-ink-2 [&>option]:bg-ink">
                        <option value="">{{ __('site.shop.sort_default') }}</option>
                        <option value="yeni" @selected(request('sirala') === 'yeni')>{{ __('site.shop.sort_new') }}</option>
                        <option value="fiyat-artan" @selected(request('sirala') === 'fiyat-artan')>{{ __('site.shop.sort_price_asc') }}</option>
                        <option value="fiyat-azalan" @selected(request('sirala') === 'fiyat-azalan')>{{ __('site.shop.sort_price_desc') }}</option>
                    </select>
                </label>
            </form>

            <hr class="rule" data-draw>

            @if ($products->isEmpty())
                <x-empty-state class="mt-12" :body="__('site.shop.empty')" />
            @else
                <div class="mt-12 grid gap-x-[clamp(1rem,2vw,2rem)] gap-y-[clamp(2.5rem,5vw,4rem)] sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                     data-reveal-stagger="0.06">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                <div class="mt-16">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </section>

</x-layout>
