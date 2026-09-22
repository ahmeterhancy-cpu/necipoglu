@use('App\Support\Locale')
@use('App\Support\Money')

<x-layout :title="__('site.cart.title')">

    <x-page-hero :title="__('site.cart.title')" />

    <section class="surface-paper pb-[clamp(4rem,10vw,8rem)]">
        <div class="shell">

            @if ($problems)
                <div class="mb-10 border border-clay p-[clamp(1.25rem,2.5vw,2rem)] rounded-md">
                    @foreach ($problems as $problem)
                        <p class="body-s" style="color:var(--color-clay)">{{ $problem }}</p>
                    @endforeach
                </div>
            @endif

            @if (! $cart || $cart->isEmpty())
                <div class="border border-dashed border-line p-[clamp(2rem,5vw,4rem)] text-center rounded-md">
                    <p class="h3">{{ __('site.cart.empty') }}</p>
                    <a href="{{ Locale::route('shop.index') }}" class="btn mt-8">{{ __('site.cart.empty_action') }}</a>
                </div>
            @else
                <div class="grid12 gap-y-12">

                    {{-- Kalemler --}}
                    <div class="col-span-12 lg:col-span-7">
                        <div class="list">
                            @foreach ($cart->items as $item)
                                @php $product = $item->product; @endphp

                                <div class="flex gap-[clamp(1rem,2vw,1.75rem)] border-b border-line py-[clamp(1.25rem,2.4vw,2rem)]">
                                    <a href="{{ Locale::route('shop.product', $product) }}" class="shot ar-square w-[clamp(5rem,9vw,7rem)] shrink-0">
                                        @if ($cover = $product->coverImage())
                                            <img src="{{ Storage::url($cover) }}" alt="" loading="lazy">
                                        @else
                                            <span class="placeholder block h-full w-full"></span>
                                        @endif
                                    </a>

                                    <div class="flex min-w-0 flex-1 flex-col">
                                        @if ($product->brand)
                                            <p class="eyebrow eyebrow-mute">{{ $product->brand->name }}</p>
                                        @endif

                                        <a href="{{ Locale::route('shop.product', $product) }}" class="h4 mt-1.5 link">{{ $product->name }}</a>

                                        <p class="body-s mute mt-1.5 tnum">
                                            {{ $product->formattedPrice() }}@if ($product->unit) / {{ $product->unit }} @endif
                                        </p>

                                        <div class="mt-auto flex flex-wrap items-center justify-between gap-4 pt-4">
                                            {{-- Adet: JS olmadan da çalışsın diye form --}}
                                            <form method="post" action="{{ Locale::route('cart.update', $item) }}" class="flex items-center gap-2">
                                                @csrf
                                                @method('patch')

                                                <label class="sr-only" for="qty-{{ $item->id }}">{{ __('site.cart.quantity') }}</label>
                                                <input id="qty-{{ $item->id }}"
                                                       type="number" name="quantity" min="1"
                                                       max="{{ config('commerce.cart.max_quantity') }}"
                                                       value="{{ $item->quantity }}"
                                                       class="w-20 border border-line bg-transparent px-3 py-2 text-center tnum outline-none transition-colors focus:border-ink rounded-md">

                                                <button type="submit" class="eyebrow link">{{ __('site.cart.update') }}</button>
                                            </form>

                                            <div class="flex items-center gap-5">
                                                <span class="tnum font-medium">{{ Money::format($item->lineTotal()) }}</span>

                                                <form method="post" action="{{ Locale::route('cart.destroy', $item) }}">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="eyebrow eyebrow-mute link">{{ __('site.cart.remove') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <a href="{{ Locale::route('shop.index') }}" class="more mt-10">{{ __('site.cart.continue') }}</a>
                    </div>

                    {{-- Özet --}}
                    <div class="col-span-12 lg:col-span-4 lg:col-start-9">
                        <div class="border border-line p-[clamp(1.5rem,3vw,2.25rem)] lg:sticky lg:top-28 rounded-md">
                            <h2 class="h3">{{ __('site.checkout.summary') }}</h2>

                            <dl class="mt-7 space-y-3">
                                <div class="flex items-baseline justify-between gap-4">
                                    <dt class="body-s">{{ __('site.cart.subtotal') }}</dt>
                                    <dd class="tnum font-medium">{{ Money::format($subtotal) }}</dd>
                                </div>
                                <div class="flex items-baseline justify-between gap-4">
                                    <dt class="body-s">{{ __('site.cart.shipping') }}</dt>
                                    <dd class="body-s mute">{{ __('site.checkout.shipping') }} →</dd>
                                </div>
                            </dl>

                            <hr class="rule my-6">

                            <div class="flex items-baseline justify-between gap-4">
                                <span class="h4">{{ __('site.cart.total') }}</span>
                                <span class="h3 tnum">{{ Money::format($subtotal) }}</span>
                            </div>

                            <p class="body-s mute mt-2">{{ __('site.cart.tax_note') }}</p>

                            <a href="{{ Locale::route('checkout') }}" class="btn mt-8 w-full justify-center" @if ($problems) aria-disabled="true" @endif>
                                {{ __('site.cart.checkout') }}
                                <svg viewBox="0 0 16 16" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path d="M2 8h11M9 4l4 4-4 4"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

</x-layout>
