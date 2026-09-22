@use('App\Support\Locale')

<x-layout :title="__('site.order.received')">

    <section class="surface-paper pt-[calc(var(--topbar)+clamp(3rem,7vw,5.5rem))] pb-[clamp(4rem,10vw,8rem)]">
        <div class="shell-narrow">

            <p class="eyebrow" data-reveal="fade">{{ __('site.order.title') }}</p>
            <h1 class="h1 mt-5" data-split>{{ __('site.order.received') }}</h1>
            <p class="lede mt-6" data-reveal>{{ __('site.order.received_note') }}</p>

            <div class="mt-10 border border-line p-[clamp(1.5rem,3vw,2.25rem)] rounded-md" data-reveal>
                <div class="flex flex-wrap items-baseline justify-between gap-x-8 gap-y-3">
                    <div>
                        <p class="eyebrow eyebrow-mute">{{ __('site.order.number') }}</p>
                        <p class="h3 mt-2 tnum">{{ $order->number }}</p>
                    </div>

                    <div class="text-right">
                        <p class="eyebrow eyebrow-mute">{{ __('site.order.date') }}</p>
                        <p class="body-m mt-2 tnum">{{ $order->created_at->translatedFormat('j F Y · H:i') }}</p>
                    </div>
                </div>

                <hr class="rule my-7">

                <dl class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <dt class="eyebrow eyebrow-mute">{{ __('site.checkout.shipping') }}</dt>
                        <dd class="body-m mt-2">
                            {{ $order->shippingLabel() }}
                            @if ($order->branch)
                                <span class="mute">— {{ $order->branch->name }}</span>
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="eyebrow eyebrow-mute">{{ __('site.checkout.payment') }}</dt>
                        <dd class="body-m mt-2">{{ $order->paymentLabel() }}</dd>
                    </div>

                    @if ($address = $order->shipping_address)
                        <div class="sm:col-span-2">
                            <dt class="eyebrow eyebrow-mute">{{ __('site.checkout.line') }}</dt>
                            <dd class="body-m mt-2">
                                {{ collect([$address['line'] ?? null, $address['district'] ?? null, $address['city'] ?? null])->filter()->implode(', ') }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Havale bilgileri --}}
            @if ($order->payment_method === 'transfer')
                <div class="mt-6 border border-line p-[clamp(1.5rem,3vw,2.25rem)] rounded-md" data-reveal>
                    <h2 class="h3">{{ __('site.order.transfer_title') }}</h2>

                    @if (empty($bankAccounts))
                        <p class="body-m mt-4">{{ __('site.order.transfer_missing') }}</p>
                        <a href="{{ Locale::route('contact') }}" class="more mt-6">{{ __('site.nav.contact') }}</a>
                    @else
                        <div class="mt-6 space-y-5">
                            @foreach ($bankAccounts as $account)
                                <div class="border-t border-line pt-5">
                                    <p class="h4">{{ $account['bank'] }}</p>
                                    <p class="body-s mute mt-1">{{ $account['holder'] }}</p>
                                    <p class="body-m mt-2 tnum">{{ $account['iban'] }}</p>
                                </div>
                            @endforeach
                        </div>

                        <p class="body-s mute mt-6">{{ __('site.order.transfer_reference') }}</p>
                    @endif
                </div>
            @endif

            {{-- Kalemler --}}
            <div class="mt-6 border border-line p-[clamp(1.5rem,3vw,2.25rem)] rounded-md" data-reveal>
                <h2 class="h3">{{ __('site.order.items') }}</h2>

                <ul class="mt-6 space-y-4">
                    @foreach ($order->items as $item)
                        <li class="flex items-baseline justify-between gap-4 border-b border-line pb-4">
                            <span class="body-m min-w-0">
                                {{ $item->name }}
                                <span class="mute tnum">× {{ $item->quantity }}</span>
                            </span>
                            <span class="body-m tnum shrink-0">{{ $item->formattedLineTotal() }}</span>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-6 space-y-3">
                    <div class="flex items-baseline justify-between gap-4">
                        <span class="body-s">{{ __('site.cart.subtotal') }}</span>
                        <span class="tnum">{{ $order->formattedSubtotal() }}</span>
                    </div>
                    <div class="flex items-baseline justify-between gap-4">
                        <span class="body-s">{{ __('site.cart.shipping') }}</span>
                        <span class="tnum">
                            {{ $order->shipping_total === 0 ? __('site.cart.free') : $order->formattedShipping() }}
                        </span>
                    </div>
                </div>

                <hr class="rule my-6">

                <div class="flex items-baseline justify-between gap-4">
                    <span class="h4">{{ __('site.cart.total') }}</span>
                    <span class="h3 tnum">{{ $order->formattedTotal() }}</span>
                </div>
            </div>

            <div class="mt-10 flex flex-wrap gap-4" data-reveal>
                <a href="{{ Locale::route('shop.index') }}" class="btn">{{ __('site.cart.continue') }}</a>

                @auth
                    <a href="{{ Locale::route('account.orders') }}" class="btn btn-line">{{ __('site.account.orders') }}</a>
                @endauth
            </div>
        </div>
    </section>

</x-layout>
