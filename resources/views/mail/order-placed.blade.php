@use('App\Support\Locale')
@use('App\Models\Setting')

<x-mail::message>
# {{ __('site.order.received') }}

{{ __('site.mail.order_intro', ['name' => $order->customer_name]) }}

**{{ __('site.order.number') }}:** {{ $order->number }}
**{{ __('site.order.date') }}:** {{ $order->created_at->translatedFormat('j F Y · H:i') }}
**{{ __('site.checkout.shipping') }}:** {{ $order->shippingLabel() }}@if ($order->branch) — {{ $order->branch->name }}@endif
**{{ __('site.checkout.payment') }}:** {{ $order->paymentLabel() }}

@if ($address = $order->shipping_address)
**{{ __('site.checkout.line') }}:** {{ collect([$address['line'] ?? null, $address['district'] ?? null, $address['city'] ?? null])->filter()->implode(', ') }}
@endif

<x-mail::table>
| {{ __('site.order.items') }} | {{ __('site.cart.quantity') }} | {{ __('site.cart.total') }} |
|:----------------------------|:------------------------------:|----------------------------:|
@foreach ($order->items as $item)
| {{ $item->name }} | {{ $item->quantity }} | {{ $item->formattedLineTotal() }} |
@endforeach
</x-mail::table>

**{{ __('site.cart.subtotal') }}:** {{ $order->formattedSubtotal() }}
**{{ __('site.cart.shipping') }}:** {{ $order->shipping_total === 0 ? __('site.cart.free') : $order->formattedShipping() }}
**{{ __('site.cart.total') }}:** {{ $order->formattedTotal() }}

@if ($order->payment_method === 'transfer')
## {{ __('site.order.transfer_title') }}

@forelse (config('commerce.bank_accounts') as $account)
**{{ $account['bank'] }}** — {{ $account['holder'] }}
{{ $account['iban'] }}

@empty
{{ __('site.order.transfer_missing') }}
@endforelse

{{ __('site.order.transfer_reference') }}
@endif

<x-mail::button :url="Locale::route('home')">
{{ Setting::get('site_name', config('site.company.short_name')) }}
</x-mail::button>

{{ __('site.mail.order_outro') }}
</x-mail::message>
