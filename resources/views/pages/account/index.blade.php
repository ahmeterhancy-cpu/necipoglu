@use('App\Support\Locale')

<x-layout :title="__('site.account.title')">

    <x-page-hero :title="__('site.account.title')" :lede="$user->name">
        <x-slot:meta>
            <x-account-nav />
        </x-slot:meta>
    </x-page-hero>

    <section class="surface-paper pb-[clamp(4rem,10vw,8rem)]">
        <div class="shell">
            <div class="grid gap-x-[clamp(1rem,2vw,2rem)] gap-y-8 md:grid-cols-3">
                <a href="{{ Locale::route('account.orders') }}" class="group border border-line p-[clamp(1.5rem,3vw,2.25rem)] transition-colors hover:border-ink rounded-md">
                    <p class="eyebrow eyebrow-mute">{{ __('site.account.orders') }}</p>
                    <p class="h2 mt-4 tnum">{{ $user->orders()->count() }}</p>
                </a>

                <a href="{{ Locale::route('account.addresses') }}" class="group border border-line p-[clamp(1.5rem,3vw,2.25rem)] transition-colors hover:border-ink rounded-md">
                    <p class="eyebrow eyebrow-mute">{{ __('site.account.addresses') }}</p>
                    <p class="h2 mt-4 tnum">{{ $addressCount }}</p>
                </a>

                <div class="border border-line p-[clamp(1.5rem,3vw,2.25rem)] rounded-md">
                    <p class="eyebrow eyebrow-mute">{{ __('site.account.email') }}</p>
                    <p class="body-m mt-4 break-words">{{ $user->email }}</p>
                    @if ($user->phone)
                        <p class="body-m mt-1 tnum">{{ $user->phone }}</p>
                    @endif
                </div>
            </div>

            @if ($orders->isNotEmpty())
                <h2 class="h3 mt-[clamp(3rem,6vw,5rem)]">{{ __('site.account.orders') }}</h2>

                <div class="list mt-8">
                    @foreach ($orders as $order)
                        <a href="{{ Locale::route('account.order', $order) }}" class="list-row">
                            <span>
                                <span class="list-title block tnum">{{ $order->number }}</span>
                                <span class="list-note block">
                                    {{ $order->created_at->translatedFormat('j F Y') }} ·
                                    {{ $order->items_count }} {{ __('site.common.products') }}
                                </span>
                            </span>

                            <span class="flex shrink-0 items-baseline gap-6">
                                <span class="list-meta">{{ $order->statusLabel() }}</span>
                                <span class="tnum font-medium">{{ $order->formattedTotal() }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

</x-layout>
