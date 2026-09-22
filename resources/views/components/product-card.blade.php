@use('App\Support\Locale')

@props(['product'])

@php $catalog = config('commerce.catalog'); @endphp

{{-- Kart bir <a> DEĞİL: satış modunda içinde "sepete ekle" formu var, form
     bağlantının içine konamaz. Bağlantı görsel ve başlığı sarar, buton ayrı
     durur. Katalog modunda alttaki "İncele" de aynı sayfaya gider. --}}
<div class="card group flex flex-col">
    <a href="{{ Locale::route('shop.product', $product) }}"
       class="block"
       data-cursor="{{ __('site.common.view') }}">

        <div class="shot card-shot ar-square" data-veil>
            @if ($cover = $product->coverImage())
                <img src="{{ Storage::url($cover) }}" alt="{{ $product->name }}" loading="lazy">
            @else
                <div class="placeholder veil-inner h-full w-full" data-label="{{ $product->category?->name }}"></div>
            @endif

            @if ($product->hasDiscount())
                <span class="eyebrow absolute left-3 top-3 rounded-sm bg-ink px-2.5 py-1.5 text-paper">%{{ $product->discountPercent() }}</span>
            @endif

            @unless ($catalog || $product->isInStock())
                <span class="eyebrow eyebrow-mute absolute right-3 top-3 rounded-sm bg-paper px-2.5 py-1.5">{{ __('site.shop.out_of_stock') }}</span>
            @endunless
        </div>

        <div class="pt-4">
            @if ($product->brand)
                <p class="eyebrow eyebrow-mute">{{ $product->brand->name }}</p>
            @endif

            <h3 class="h4 card-title {{ $product->brand ? 'mt-2' : '' }}">{{ $product->name }}</h3>

            <p class="mt-2.5 flex items-baseline gap-3">
                <span class="tnum body-s font-medium">{{ $product->formattedPrice() }}</span>

                @if ($product->hasDiscount())
                    <span class="tnum body-s mute line-through">{{ $product->formattedComparePrice() }}</span>
                @endif

                @if ($product->unit)
                    <span class="body-s mute">/ {{ $product->unit }}</span>
                @endif
            </p>
        </div>
    </a>

    <div class="mt-auto pt-4">
        @if ($catalog)
            {{-- Sekme sırasında kartın bağlantısı zaten var; ikinci durak
                 olmasın diye tabindex -1, ekran okuyucudan da gizli. --}}
            <a href="{{ Locale::route('shop.product', $product) }}"
               class="btn btn-line w-full justify-center"
               tabindex="-1"
               aria-hidden="true">{{ __('site.shop.inspect') }}</a>
        @elseif ($product->isInStock())
            <form method="post" action="{{ Locale::route('cart.store', $product) }}">
                @csrf
                <button type="submit" class="btn btn-line w-full justify-center">{{ __('site.cart.add') }}</button>
            </form>
        @else
            <span class="btn btn-line pointer-events-none w-full justify-center opacity-40">{{ __('site.shop.out_of_stock') }}</span>
        @endif
    </div>
</div>
