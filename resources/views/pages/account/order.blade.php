@use('App\Support\Locale')

<x-layout :title="$order->number">

    <section class="surface-paper pt-[calc(var(--topbar)+clamp(3rem,7vw,5.5rem))] pb-[clamp(4rem,10vw,8rem)]">
        <div class="shell-narrow">

            <a href="{{ Locale::route('account.orders') }}" class="eyebrow eyebrow-mute link" data-reveal="fade">
                ← {{ __('site.account.orders') }}
            </a>

            @if (session('account.done'))
                <p class="body-m mt-8 border border-line p-5 rounded-md">{{ session('account.done') }}</p>
            @endif

            @if (session('account.error'))
                <p class="body-m mt-8 border border-clay p-5 rounded-md" style="color:var(--color-clay)">{{ session('account.error') }}</p>
            @endif

            <div class="mt-8 flex flex-wrap items-baseline justify-between gap-x-8 gap-y-3">
                <h1 class="h1 tnum" data-split>{{ $order->number }}</h1>
                <span class="eyebrow">{{ $order->statusLabel() }}</span>
            </div>

            <div class="mt-10 border border-line p-[clamp(1.5rem,3vw,2.25rem)] rounded-md" data-reveal>
                <dl class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <dt class="eyebrow eyebrow-mute">{{ __('site.order.date') }}</dt>
                        <dd class="body-m mt-2 tnum">{{ $order->created_at->translatedFormat('j F Y · H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow eyebrow-mute">{{ __('site.checkout.payment') }}</dt>
                        <dd class="body-m mt-2">{{ $order->paymentLabel() }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow eyebrow-mute">{{ __('site.checkout.shipping') }}</dt>
                        <dd class="body-m mt-2">
                            {{ $order->shippingLabel() }}
                            @if ($order->branch)<span class="mute">— {{ $order->branch->name }}</span>@endif
                        </dd>
                    </div>

                    @if ($address = $order->shipping_address)
                        <div>
                            <dt class="eyebrow eyebrow-mute">{{ __('site.checkout.line') }}</dt>
                            <dd class="body-m mt-2">
                                {{ collect([$address['line'] ?? null, $address['district'] ?? null, $address['city'] ?? null])->filter()->implode(', ') }}
                            </dd>
                        </div>
                    @endif

                    @if ($order->note)
                        <div class="sm:col-span-2">
                            <dt class="eyebrow eyebrow-mute">{{ __('site.checkout.note') }}</dt>
                            <dd class="body-m mt-2 whitespace-pre-line">{{ $order->note }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            @if ($order->payment_method === 'transfer' && $order->payment_status === 'unpaid')
                <div class="mt-6 border border-line p-[clamp(1.5rem,3vw,2.25rem)] rounded-md" data-reveal>
                    <h2 class="h3">{{ __('site.order.transfer_title') }}</h2>

                    @if (empty($bankAccounts))
                        <p class="body-m mt-4">{{ __('site.order.transfer_missing') }}</p>
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

            <div class="mt-6 border border-line p-[clamp(1.5rem,3vw,2.25rem)] rounded-md" data-reveal>
                <h2 class="h3">{{ __('site.order.items') }}</h2>

                <ul class="mt-6">
                    @foreach ($order->items as $item)
                        <li class="flex items-baseline justify-between gap-4 border-b border-line py-4">
                            <span class="body-m min-w-0">
                                {{ $item->name }}
                                <span class="mute tnum">× {{ $item->quantity }}</span>
                                @if ($item->unit)<span class="mute"> / {{ $item->unit }}</span>@endif
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
                        <span class="tnum">{{ $order->shipping_total === 0 ? __('site.cart.free') : $order->formattedShipping() }}</span>
                    </div>
                </div>

                <hr class="rule my-6">

                <div class="flex items-baseline justify-between gap-4">
                    <span class="h4">{{ __('site.cart.total') }}</span>
                    <span class="h3 tnum">{{ $order->formattedTotal() }}</span>
                </div>
            </div>

            @if ($order->isCancellable())
                <form method="post" action="{{ Locale::route('account.order.cancel', $order) }}" class="mt-10">
                    @csrf
                    <button type="submit" class="btn btn-line">{{ __('site.order.cancel') }}</button>
                </form>
            @endif
        </div>
    </section>

</x-layout>
