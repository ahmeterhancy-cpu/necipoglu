@use('App\Support\Locale')

@php
    $schema = [
        \App\Support\Schema::product($product),
        \App\Support\Schema::breadcrumbs([
            ['name' => __('site.shop.title'), 'url' => Locale::route('shop.index')],
            ['name' => $product->category->name, 'url' => Locale::route('shop.category', $product->category)],
            ['name' => $product->name, 'url' => url()->current()],
        ]),
    ];

    $catalog = config('commerce.catalog');
    $whatsapp = preg_replace('/[^\d]/', '', (string) \App\Models\Setting::get('whatsapp', config('site.company.whatsapp')));
@endphp

<x-layout :title="$product->name" :description="$product->summary" :schema="$schema">

    <section class="surface-paper pt-[clamp(7rem,15vh,10rem)]">
        <div class="shell">

            {{-- Kırıntı yolu --}}
            <nav class="eyebrow eyebrow-mute flex flex-wrap items-center gap-x-3 gap-y-2" aria-label="breadcrumb" data-reveal="fade">
                <a href="{{ Locale::route('shop.index') }}" class="link">{{ __('site.shop.title') }}</a>
                <span aria-hidden="true">/</span>
                <a href="{{ Locale::route('shop.category', $product->category) }}" class="link">{{ $product->category->name }}</a>
            </nav>

            <div class="grid12 mt-10 gap-y-12">

                {{-- Görsel sütunu --}}
                <div class="col-span-12 lg:col-span-7">
                    @php $images = $product->images ?? []; @endphp

                    <div class="shot ar-square">
                        @if ($images)
                            <img src="{{ Storage::url($images[0]) }}" alt="{{ $product->name }}" fetchpriority="high">
                        @else
                            <div class="placeholder h-full w-full">
                                <span class="sr-only">{{ $product->category?->name }}</span>
                            </div>
                        @endif
                    </div>

                    @if (count($images) > 1)
                        <div class="mt-[clamp(1rem,2vw,2rem)] grid grid-cols-3 gap-[clamp(1rem,2vw,2rem)]">
                            @foreach (array_slice($images, 1) as $image)
                                <div class="shot ar-square" data-reveal="scale">
                                    <img src="{{ Storage::url($image) }}" alt="{{ $product->name }}" loading="lazy">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Bilgi sütunu --}}
                <div class="col-span-12 lg:col-span-4 lg:col-start-9">
                    @if ($product->brand)
                        <p class="eyebrow" data-reveal="fade">{{ $product->brand->name }}</p>
                    @endif

                    <h1 class="h3 mt-5" data-split>{{ $product->name }}</h1>

                    @if ($product->summary)
                        <p class="lede mute mt-6" data-reveal>{{ $product->summary }}</p>
                    @endif

                    <hr class="rule my-9" data-draw>

                    <div data-reveal>
                        <p class="flex flex-wrap items-baseline gap-4">
                            <span class="h4 tnum">{{ $product->formattedPrice() }}</span>

                            @if ($product->hasDiscount())
                                <span class="tnum body-s opacity-45 line-through">{{ $product->formattedComparePrice() }}</span>
                                <span class="eyebrow rounded-sm bg-ink px-2.5 py-1.5 text-paper">%{{ $product->discountPercent() }}</span>
                            @endif

                            @if ($product->unit)
                                <span class="body-s opacity-45">/ {{ $product->unit }}</span>
                            @endif
                        </p>

                        @if ($catalog)
                            @if ($product->sku)
                                <p class="eyebrow eyebrow-mute mt-4">{{ $product->sku }}</p>
                            @endif
                        @else
                            <p class="eyebrow mt-4 {{ $product->isInStock() ? '' : 'eyebrow-mute' }}">
                                {{ $product->isInStock() ? __('site.shop.in_stock') : __('site.shop.out_of_stock') }}
                                @if ($product->sku)
                                    <span class="eyebrow-mute">· {{ $product->sku }}</span>
                                @endif
                            </p>
                        @endif
                    </div>

                    <div class="mt-9" data-reveal>
                        @if ($catalog)
                            {{-- Katalog: sipariş yok, talep var. Konu satırı ürün
                                 adıyla dolar; WhatsApp mesajı da ürünü taşır. --}}
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ Locale::route('contact') }}?urun={{ $product->slug }}" class="btn flex-1 justify-center">
                                    {{ __('site.shop.inquire') }}
                                </a>

                                @if ($whatsapp)
                                    <a href="https://wa.me/{{ $whatsapp }}?text={{ rawurlencode($product->name.($product->sku ? ' ('.$product->sku.')' : '')) }}"
                                       class="btn btn-line flex-1 justify-center"
                                       target="_blank"
                                       rel="noopener"
                                       data-no-veil>
                                        {{ __('site.shop.whatsapp') }}
                                    </a>
                                @endif
                            </div>

                            <p class="body-s mute mt-5">{{ __('site.shop.catalog_note') }}</p>
                        @else
                        @if (session('cart.error'))
                            <p class="body-s mb-5" style="color:var(--color-clay)">{{ session('cart.error') }}</p>
                        @endif

                        @if (session('cart.added'))
                            <div class="mb-5 flex flex-wrap items-center gap-x-6 gap-y-2 border border-line p-4 rounded-md">
                                <span class="body-s">{{ __('site.cart.added', ['name' => session('cart.added')]) }}</span>
                                <a href="{{ Locale::route('cart') }}" class="eyebrow link">{{ __('site.cart.title') }} →</a>
                            </div>
                        @endif

                        @if ($product->isInStock())
                            <form method="post" action="{{ Locale::route('cart.store', $product) }}" class="flex flex-wrap items-stretch gap-3">
                                @csrf

                                <label class="sr-only" for="qty">{{ __('site.cart.quantity') }}</label>
                                <input id="qty" type="number" name="quantity" value="1" min="1"
                                       max="{{ config('commerce.cart.max_quantity') }}"
                                       class="w-24 border border-line bg-transparent px-3 text-center tnum outline-none transition-colors focus:border-ink rounded-md">

                                {{-- Magnetic efekti bilerek yok: buton imlece doğru
                                     kayınca ana satın alma aksiyonu kaygan hissettiriyor. --}}
                                <button type="submit" class="btn flex-1 justify-center">
                                    {{ __('site.cart.add') }}
                                </button>
                            </form>
                        @else
                            <a href="{{ Locale::route('contact') }}?urun={{ $product->slug }}" class="btn btn-line">
                                {{ __('site.nav.contact') }}
                            </a>
                        @endif
                        @endif
                    </div>

                    @if ($product->description)
                        <div class="body-m mute mt-10 whitespace-pre-line" data-reveal>{{ $product->description }}</div>
                    @endif

                    @if ($specs = $product->specs)
                        <div class="mt-10" data-reveal>
                            <p class="eyebrow">{{ __('site.shop.specs') }}</p>

                            <dl class="mt-6">
                                @foreach ($specs as $spec)
                                    <div class="flex items-baseline justify-between gap-6 border-t border-line py-4">
                                        <dt class="body-s opacity-60">{{ $spec['label'] ?? '' }}</dt>
                                        <dd class="body-s text-right">{{ $spec['value'] ?? '' }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="surface-paper band">
            <div class="shell">
                <p class="eyebrow" data-reveal="fade">{{ __('site.shop.related') }}</p>

                <div class="mt-10 grid gap-x-[clamp(1rem,2vw,2rem)] gap-y-10 sm:grid-cols-2 lg:grid-cols-4"
                     data-reveal-stagger="0.07">
                    @foreach ($related as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</x-layout>
