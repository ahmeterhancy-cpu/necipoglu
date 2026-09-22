@use('App\Support\Locale')

<x-layout :title="__('site.account.addresses')">

    <x-page-hero :title="__('site.account.addresses')">
        <x-slot:meta>
            <x-account-nav />
        </x-slot:meta>
    </x-page-hero>

    <section class="surface-paper pb-[clamp(4rem,10vw,8rem)]">
        <div class="shell">

            @if (session('account.done'))
                <p class="body-m mb-8 border border-line p-5 rounded-md">{{ session('account.done') }}</p>
            @endif

            <div class="grid12 gap-y-12">
                {{-- Kayıtlı adresler --}}
                <div class="col-span-12 lg:col-span-7">
                    @if ($addresses->isEmpty())
                        <div class="border border-dashed border-line p-[clamp(1.75rem,4vw,3rem)] rounded-md">
                            <p class="body-m">{{ __('site.account.no_addresses') }}</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($addresses as $address)
                                <div class="border border-line p-[clamp(1.25rem,2.5vw,2rem)] rounded-md">
                                    <div class="flex flex-wrap items-baseline justify-between gap-x-6 gap-y-2">
                                        <p class="h4">{{ $address->title ?: $address->city }}</p>
                                        @if ($address->is_default)
                                            <span class="eyebrow">{{ __('site.account.default_address') }}</span>
                                        @endif
                                    </div>

                                    <p class="body-s mute mt-3">{{ $address->full_name }} · {{ $address->phone }}</p>
                                    <p class="body-m mt-2">{{ $address->oneLine() }}</p>

                                    <form method="post" action="{{ Locale::route('account.addresses.destroy', $address) }}" class="mt-5">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="eyebrow eyebrow-mute link">{{ __('site.account.delete') }}</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Yeni adres --}}
                <div class="col-span-12 lg:col-span-4 lg:col-start-9">
                    <div class="border border-line p-[clamp(1.5rem,3vw,2.25rem)] rounded-md">
                        <h2 class="h3">{{ __('site.account.add_address') }}</h2>

                        <form method="post" action="{{ Locale::route('account.addresses.store') }}" class="mt-7 space-y-6">
                            @csrf

                            <label class="block">
                                <span class="eyebrow eyebrow-mute">{{ __('site.account.address_title') }}</span>
                                <input type="text" name="title" maxlength="60" value="{{ old('title') }}"
                                       placeholder="{{ __('site.account.address_title_hint') }}"
                                       class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                            </label>

                            <label class="block">
                                <span class="eyebrow eyebrow-mute">{{ __('site.account.name') }} *</span>
                                <input type="text" name="full_name" required maxlength="120"
                                       value="{{ old('full_name', auth()->user()->name) }}"
                                       class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                                @error('full_name') <span class="body-s mt-2 block" style="color:var(--color-clay)">{{ $message }}</span> @enderror
                            </label>

                            <label class="block">
                                <span class="eyebrow eyebrow-mute">{{ __('site.account.phone') }} *</span>
                                <input type="tel" name="phone" required maxlength="40"
                                       value="{{ old('phone', auth()->user()->phone) }}"
                                       class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                                @error('phone') <span class="body-s mt-2 block" style="color:var(--color-clay)">{{ $message }}</span> @enderror
                            </label>

                            <label class="block">
                                <span class="eyebrow eyebrow-mute">{{ __('site.checkout.city') }} *</span>
                                <input type="text" name="city" required maxlength="80" value="{{ old('city') }}"
                                       class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                                @error('city') <span class="body-s mt-2 block" style="color:var(--color-clay)">{{ $message }}</span> @enderror
                            </label>

                            <label class="block">
                                <span class="eyebrow eyebrow-mute">{{ __('site.checkout.district') }}</span>
                                <input type="text" name="district" maxlength="80" value="{{ old('district') }}"
                                       class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                            </label>

                            <label class="block">
                                <span class="eyebrow eyebrow-mute">{{ __('site.checkout.line') }} *</span>
                                <textarea name="line" rows="3" required maxlength="400"
                                          class="mt-3 w-full resize-y border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">{{ old('line') }}</textarea>
                                @error('line') <span class="body-s mt-2 block" style="color:var(--color-clay)">{{ $message }}</span> @enderror
                            </label>

                            <label class="flex items-center gap-3">
                                <input type="checkbox" name="is_default" value="1" @checked(old('is_default'))>
                                <span class="body-s">{{ __('site.account.default_address') }}</span>
                            </label>

                            <button type="submit" class="btn w-full justify-center">{{ __('site.account.save') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-layout>
