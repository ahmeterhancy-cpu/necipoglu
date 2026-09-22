@use('App\Support\Locale')

<x-layout :title="__('site.account.orders')">

    <x-page-hero :title="__('site.account.orders')">
        <x-slot:meta>
            <x-account-nav />
        </x-slot:meta>
    </x-page-hero>

    <section class="surface-paper pb-[clamp(4rem,10vw,8rem)]">
        <div class="shell">
            @if ($orders->isEmpty())
                <div class="border border-dashed border-line p-[clamp(2rem,5vw,4rem)] text-center rounded-md">
                    <p class="h3">{{ __('site.account.no_orders') }}</p>
                    <a href="{{ Locale::route('shop.index') }}" class="btn mt-8">{{ __('site.cart.empty_action') }}</a>
                </div>
            @else
                <div class="list">
                    @foreach ($orders as $order)
                        <a href="{{ Locale::route('account.order', $order) }}" class="list-row">
                            <span>
                                <span class="list-title block tnum">{{ $order->number }}</span>
                                <span class="list-note block">
                                    {{ $order->created_at->translatedFormat('j F Y · H:i') }} ·
                                    {{ $order->items_count }} {{ __('site.common.products') }} ·
                                    {{ $order->shippingLabel() }}
                                </span>
                            </span>

                            <span class="flex shrink-0 items-baseline gap-6">
                                <span class="list-meta">{{ $order->statusLabel() }}</span>
                                <span class="tnum font-medium">{{ $order->formattedTotal() }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>

                <div class="mt-12">{{ $orders->links() }}</div>
            @endif
        </div>
    </section>

</x-layout>
