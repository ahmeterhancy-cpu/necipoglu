@props([
    'eyebrow' => null,
    'title' => '',
    'lede' => null,
    'meta' => null,
])

{{-- Alt sayfa açılışı: açık zemin, ölçülü başlık, altında ince çizgi. --}}
<section {{ $attributes->merge(['class' => 'surface-paper pt-[calc(var(--topbar)+clamp(3rem,7vw,5.5rem))] pb-[clamp(2.5rem,5vw,4rem)]']) }}>
    <div class="shell">
        @if ($eyebrow)
            <p class="eyebrow" data-reveal="fade">{{ $eyebrow }}</p>
        @endif

        {{-- Ölçü 24ch iken virgüllü bir başlıkta son kelime tek başına alt
         satıra düşüyordu. 30ch iki-üç kelimelik başlıkları bozmuyor. --}}
        <h1 class="h1 mt-5 max-w-[30ch]" data-split>{{ $title }}</h1>

        @if ($lede)
            <p class="lede mt-7" data-reveal>{{ $lede }}</p>
        @endif

        @if ($meta)
            <div class="mt-9">{{ $meta }}</div>
        @endif

        <hr class="rule mt-[clamp(2.5rem,5vw,4rem)]" data-draw>
    </div>
</section>
