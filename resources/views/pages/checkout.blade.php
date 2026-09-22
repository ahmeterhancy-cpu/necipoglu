@use('App\Support\Locale')
@use('App\Support\Money')

@php
    $tr = app()->getLocale() === 'tr';
    $selectedShipping = old('shipping_method', array_key_first($shippingMethods));

    // Betiğe geçecek veri burada hazırlanır — @json içine hesaplama yazmak
    // Blade'in ifade ayrıştırıcısını bozuyor.
    $shippingRates = collect($shippingMethods)
        ->map(fn ($method) => [
            'price' => $method['price'],
            'free_over' => $method['free_over'],
            'needs_address' => $method['needs_address'],
        ])
        ->all();
@endphp

<x-layout :title="__('site.checkout.title')">

    <x-page-hero :title="__('site.checkout.title')" />

    <section class="surface-paper pb-[clamp(4rem,10vw,8rem)]">
        <div class="shell">

            @if (session('checkout.error'))
                <div class="mb-10 border border-clay p-[clamp(1.25rem,2.5vw,2rem)] rounded-md">
                    <p class="body-m" style="color:var(--color-clay)">{{ session('checkout.error') }}</p>
                </div>
            @endif

            @if ($problems)
                <div class="mb-10 border border-clay p-[clamp(1.25rem,2.5vw,2rem)] rounded-md">
                    @foreach ($problems as $problem)
                        <p class="body-s" style="color:var(--color-clay)">{{ $problem }}</p>
                    @endforeach
                </div>
            @endif

            @guest
                <p class="body-m mb-10">
                    {{ __('site.checkout.guest_note') }}
                    <span class="mute">{{ __('site.checkout.have_account') }}</span>
                    <a href="{{ Locale::route('login') }}" class="link">{{ __('site.checkout.login') }}</a>
                </p>
            @endguest

            <form method="post" action="{{ Locale::route('checkout.store') }}" data-checkout>
                @csrf

                <div class="hidden" aria-hidden="true">
                    <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                <div class="grid12 gap-y-14">
                    <div class="col-span-12 lg:col-span-7">

                        {{-- İletişim --}}
                        <fieldset>
                            <legend class="h3">{{ __('site.checkout.contact') }}</legend>

                            <div class="mt-7 grid gap-x-8 gap-y-7 sm:grid-cols-2">
                                <label class="block">
                                    <span class="eyebrow eyebrow-mute">{{ __('site.account.name') }} *</span>
                                    <input type="text" name="customer_name" required maxlength="120"
                                           value="{{ old('customer_name', auth()->user()?->name) }}"
                                           class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                                    @error('customer_name') <span class="body-s mt-2 block" style="color:var(--color-clay)">{{ $message }}</span> @enderror
                                </label>

                                <label class="block">
                                    <span class="eyebrow eyebrow-mute">{{ __('site.account.phone') }} *</span>
                                    <input type="tel" name="customer_phone" required maxlength="40"
                                           value="{{ old('customer_phone', auth()->user()?->phone) }}"
                                           class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                                    @error('customer_phone') <span class="body-s mt-2 block" style="color:var(--color-clay)">{{ $message }}</span> @enderror
                                </label>

                                <label class="block sm:col-span-2">
                                    <span class="eyebrow eyebrow-mute">{{ __('site.account.email') }}</span>
                                    <input type="email" name="customer_email" maxlength="160"
                                           value="{{ old('customer_email', auth()->user()?->email) }}"
                                           class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                                    @error('customer_email') <span class="body-s mt-2 block" style="color:var(--color-clay)">{{ $message }}</span> @enderror
                                </label>
                            </div>
                        </fieldset>

                        {{-- Teslimat --}}
                        <fieldset class="mt-[clamp(2.5rem,5vw,4rem)]">
                            <legend class="h3">{{ __('site.checkout.shipping') }}</legend>

                            <div class="mt-7 space-y-3">
                                @foreach ($shippingMethods as $key => $method)
                                    <label class="flex cursor-pointer gap-4 border border-line p-[clamp(1rem,2vw,1.5rem)] transition-colors hover:border-ink has-[:checked]:border-ink rounded-md">
                                        <input type="radio" name="shipping_method" value="{{ $key }}"
                                               @checked($selectedShipping === $key)
                                               class="mt-1.5 shrink-0" data-shipping="{{ $method['needs_address'] ? 'address' : 'branch' }}">

                                        <span class="min-w-0">
                                            <span class="h4 block">{{ $method['label'][app()->getLocale()] ?? $method['label']['tr'] }}</span>
                                            <span class="body-s mute mt-1.5 block">{{ $method['note'][app()->getLocale()] ?? $method['note']['tr'] }}</span>
                                            <span class="eyebrow mt-3 block">
                                                @if ($method['price'] === 0)
                                                    {{ __('site.cart.free') }}
                                                @else
                                                    {{ Money::format($method['price']) }}
                                                    @if ($method['free_over'])
                                                        <span class="eyebrow-mute">· {{ Money::format($method['free_over']) }}{{ $tr ? ' üzeri ücretsiz' : '+ free' }}</span>
                                                    @endif
                                                @endif
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            {{-- Adres alanları — adrese teslimat seçilince görünür --}}
                            {{-- JS kapalıysa iki alan grubu da görünür kalır ve
                                 form yine çalışır; JS varsa seçime göre gizlenir. --}}
                            <div class="mt-8 grid gap-x-8 gap-y-7 sm:grid-cols-2" data-address-fields>
                                @auth
                                    @if ($addresses->isNotEmpty())
                                        <label class="block sm:col-span-2">
                                            <span class="eyebrow eyebrow-mute">{{ __('site.checkout.use_address') }}</span>
                                            <select data-address-picker
                                                    class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                                                <option value="">—</option>
                                                @foreach ($addresses as $address)
                                                    <option value="{{ $address->id }}"
                                                            data-city="{{ $address->city }}"
                                                            data-district="{{ $address->district }}"
                                                            data-line="{{ $address->line }}">
                                                        {{ $address->title ?: $address->city }} — {{ $address->oneLine() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </label>
                                    @endif
                                @endauth

                                <label class="block">
                                    <span class="eyebrow eyebrow-mute">{{ __('site.checkout.city') }} *</span>
                                    <input type="text" name="city" maxlength="80" value="{{ old('city') }}"
                                           class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                                    @error('city') <span class="body-s mt-2 block" style="color:var(--color-clay)">{{ $message }}</span> @enderror
                                </label>

                                <label class="block">
                                    <span class="eyebrow eyebrow-mute">{{ __('site.checkout.district') }}</span>
                                    <input type="text" name="district" maxlength="80" value="{{ old('district') }}"
                                           class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                                </label>

                                <label class="block sm:col-span-2">
                                    <span class="eyebrow eyebrow-mute">{{ __('site.checkout.line') }} *</span>
                                    <textarea name="line" rows="3" maxlength="400"
                                              class="mt-3 w-full resize-y border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">{{ old('line') }}</textarea>
                                    @error('line') <span class="body-s mt-2 block" style="color:var(--color-clay)">{{ $message }}</span> @enderror
                                </label>
                            </div>

                            {{-- Şube seçimi — şubeden teslim alma seçilince görünür --}}
                            <div class="mt-8" data-branch-fields>
                                <label class="block">
                                    <span class="eyebrow eyebrow-mute">{{ __('site.checkout.branch') }} *</span>
                                    <select name="branch_id"
                                            class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>
                                                {{ $branch->name }} — {{ $branch->address }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id') <span class="body-s mt-2 block" style="color:var(--color-clay)">{{ $message }}</span> @enderror
                                </label>
                            </div>
                        </fieldset>

                        {{-- Ödeme --}}
                        <fieldset class="mt-[clamp(2.5rem,5vw,4rem)]">
                            <legend class="h3">{{ __('site.checkout.payment') }}</legend>

                            <div class="mt-7 space-y-3">
                                @foreach ($paymentMethods as $key => $method)
                                    <label class="flex cursor-pointer gap-4 border border-line p-[clamp(1rem,2vw,1.5rem)] transition-colors hover:border-ink has-[:checked]:border-ink rounded-md">
                                        <input type="radio" name="payment_method" value="{{ $key }}"
                                               @checked(old('payment_method', array_key_first($paymentMethods)) === $key)
                                               class="mt-1.5 shrink-0">

                                        <span class="min-w-0">
                                            <span class="h4 block">{{ $method['label'][app()->getLocale()] ?? $method['label']['tr'] }}</span>
                                            <span class="body-s mute mt-1.5 block">{{ $method['note'][app()->getLocale()] ?? $method['note']['tr'] }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        {{-- Not --}}
                        <fieldset class="mt-[clamp(2.5rem,5vw,4rem)]">
                            <legend class="h3">{{ __('site.checkout.note') }}</legend>

                            <textarea name="note" rows="4" maxlength="2000"
                                      placeholder="{{ __('site.checkout.note_hint') }}"
                                      class="mt-6 w-full resize-y border border-line bg-transparent p-4 outline-none transition-colors focus:border-ink rounded-md">{{ old('note') }}</textarea>
                        </fieldset>
                    </div>

                    {{-- Özet --}}
                    <div class="col-span-12 lg:col-span-4 lg:col-start-9">
                        <div class="border border-line p-[clamp(1.5rem,3vw,2.25rem)] lg:sticky lg:top-28 rounded-md">
                            <h2 class="h3">{{ __('site.checkout.summary') }}</h2>

                            <ul class="mt-7 space-y-4">
                                @foreach ($cart->items as $item)
                                    <li class="flex items-baseline justify-between gap-4">
                                        <span class="body-s min-w-0">
                                            {{ $item->product->name }}
                                            <span class="mute tnum">× {{ $item->quantity }}</span>
                                        </span>
                                        <span class="body-s tnum shrink-0">{{ Money::format($item->lineTotal()) }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <hr class="rule my-6">

                            <div class="flex items-baseline justify-between gap-4">
                                <span class="body-s">{{ __('site.cart.subtotal') }}</span>
                                <span class="tnum">{{ Money::format($subtotal) }}</span>
                            </div>

                            <div class="mt-3 flex items-baseline justify-between gap-4">
                                <span class="body-s">{{ __('site.cart.shipping') }}</span>
                                <span class="tnum" data-shipping-total>—</span>
                            </div>

                            <hr class="rule my-6">

                            <div class="flex items-baseline justify-between gap-4">
                                <span class="h4">{{ __('site.cart.total') }}</span>
                                <span class="h3 tnum" data-grand-total>{{ Money::format($subtotal) }}</span>
                            </div>

                            <p class="body-s mute mt-2">{{ __('site.cart.tax_note') }}</p>

                            <button type="submit" class="btn mt-8 w-full justify-center">
                                {{ __('site.checkout.place') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <x-slot:head>
        <script type="module">
            /* Teslimat yöntemine göre alan gösterimi ve toplam güncellemesi.
               Sunucu tarafı doğrulama bunlardan bağımsız çalışır — JS kapalıysa
               form yine gönderilir, alanlar görünür kalır. */
            const form = document.querySelector('[data-checkout]');
            if (form) {
                const addressFields = form.querySelector('[data-address-fields]');
                const branchFields = form.querySelector('[data-branch-fields]');
                const shippingOut = form.querySelector('[data-shipping-total]');
                const grandOut = form.querySelector('[data-grand-total]');

                const rates = @json($shippingRates);
                const subtotal = {{ $subtotal }};
                const money = new Intl.NumberFormat('{{ app()->getLocale() }}-CY', {
                    style: 'currency', currency: '{{ config('commerce.currency') }}', maximumFractionDigits: 2,
                });

                const apply = () => {
                    const checked = form.querySelector('input[name="shipping_method"]:checked');
                    if (!checked) return;

                    const rate = rates[checked.value];
                    const needsAddress = rate.needs_address;

                    addressFields.hidden = !needsAddress;
                    branchFields.hidden = needsAddress;

                    const free = rate.free_over !== null && subtotal >= rate.free_over;
                    const cost = free ? 0 : rate.price;

                    shippingOut.textContent = cost === 0 ? '{{ __('site.cart.free') }}' : money.format(cost / 100);
                    grandOut.textContent = money.format((subtotal + cost) / 100);
                };

                form.querySelectorAll('input[name="shipping_method"]').forEach((input) => {
                    input.addEventListener('change', apply);
                });

                // Kayıtlı adres seçilince alanları doldur.
                form.querySelector('[data-address-picker]')?.addEventListener('change', (e) => {
                    const option = e.target.selectedOptions[0];
                    if (!option?.value) return;

                    form.querySelector('[name="city"]').value = option.dataset.city ?? '';
                    form.querySelector('[name="district"]').value = option.dataset.district ?? '';
                    form.querySelector('[name="line"]').value = option.dataset.line ?? '';
                });

                apply();
            }
        </script>
    </x-slot:head>

</x-layout>
