@use('App\Support\Locale')
@use('App\Models\Setting')

@php
    $schema = $branches->map(fn ($branch) => \App\Support\Schema::branch($branch))->all();
@endphp

<x-layout :title="__('site.contact.title')" :schema="$schema">

    <x-page-hero :eyebrow="__('site.contact.title')"
                 :title="__('site.home.branches_title')"
                 :lede="__('site.home.cta_body')" />

    {{-- Şubeler: sol tarafta seçilebilir liste, sağda tek harita.
         Üç ayrı iframe yerine tek iframe — hem hızlı hem derli toplu.
         Harita ancak bir şube seçildiğinde ya da görünür olduğunda yüklenir. --}}
    <section class="surface-paper band" data-branches>
        <div class="shell">
            <div class="grid12 gap-y-10">

                <div class="col-span-12 lg:col-span-5">
                    @foreach ($branches as $branch)
                        <div class="list-row cursor-pointer"
                             data-branch
                             data-branch-map="{{ $branch->mapEmbedUrl() }}"
                             data-branch-name="{{ $branch->name }}"
                             @if ($loop->first) data-branch-active @endif>

                            <div class="min-w-0">
                                <p class="list-title">{{ $branch->name }}</p>
                                <p class="list-note">{{ $branch->kind }}</p>
                                <p class="body-s mute mt-4">{{ $branch->address }}</p>

                                <p class="mt-4 flex flex-wrap gap-x-6 gap-y-2">
                                    @foreach ($branch->phones ?? [] as $phone)
                                        <a href="tel:{{ $branch->dialable($phone) }}" data-no-veil class="body-s link tnum">{{ $phone }}</a>
                                    @endforeach
                                </p>

                                <p class="mt-5 flex flex-wrap items-center gap-x-6 gap-y-2">
                                    <button type="button" class="eyebrow link" data-branch-show>
                                        {{ app()->getLocale() === 'tr' ? 'Haritada göster' : 'Show on map' }}
                                    </button>

                                    <a href="{{ $branch->directionsUrl() }}" target="_blank" rel="noopener" data-no-veil class="eyebrow link">
                                        {{ __('site.common.get_directions') }}
                                    </a>

                                    @if ($branch->whatsapp)
                                        <a href="https://wa.me/{{ preg_replace('/[^\d]/', '', $branch->whatsapp) }}"
                                           target="_blank" rel="noopener" data-no-veil class="eyebrow link text-clay">
                                            {{ __('site.common.whatsapp') }}
                                        </a>
                                    @endif
                                </p>
                            </div>

                            <span></span>
                        </div>
                    @endforeach
                </div>

                <div class="col-span-12 lg:col-span-6 lg:col-start-7">
                    <div class="shot ar-square border border-line lg:sticky lg:top-28 lg:aspect-[4/5] rounded-md" data-branch-frame>
                        <div class="placeholder h-full w-full">
                            <span class="sr-only" style="color:#5F5F68;border-color:#D8D3CA">{{ __('site.common.get_directions') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Form --}}
    <section class="surface-paper band">
        <div class="shell">
            <div class="grid12 gap-y-12">
                <div class="col-span-12 lg:col-span-4">
                    <p class="eyebrow" data-reveal="fade">{{ __('site.contact.form_title') }}</p>
                    <h2 class="h3 mt-6" data-split>{{ __('site.home.cta_title') }}</h2>

                    <div class="mt-8 space-y-3">
                        <a href="mailto:{{ Setting::get('email', config('site.company.email')) }}" data-no-veil class="body-m link block">
                            {{ Setting::get('email', config('site.company.email')) }}
                        </a>
                    </div>
                </div>

                <div class="col-span-12 lg:col-span-7 lg:col-start-6">
                    @if (session('contact.sent'))
                        <div class="border border-clay p-[clamp(1.5rem,3vw,2.5rem)] rounded-md" data-reveal>
                            <p class="h4">{{ __('site.contact.sent') }}</p>
                        </div>
                    @else
                        <form method="post" action="{{ Locale::route('contact.store') }}" class="grid gap-x-8 gap-y-7 sm:grid-cols-2" data-reveal>
                            @csrf

                            {{-- Bal küpü — ekran okuyucudan ve gözden gizli, botlar doldurur --}}
                            <div class="hidden" aria-hidden="true">
                                <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                            </div>

                            <label class="block">
                                <span class="eyebrow eyebrow-mute">{{ __('site.contact.name') }} *</span>
                                <input type="text" name="name" value="{{ old('name') }}" required maxlength="120"
                                       class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                                @error('name') <span class="body-s mt-2 block text-clay">{{ $message }}</span> @enderror
                            </label>

                            <label class="block">
                                <span class="eyebrow eyebrow-mute">{{ __('site.contact.phone') }}</span>
                                <input type="tel" name="phone" value="{{ old('phone') }}" maxlength="40"
                                       class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                            </label>

                            <label class="block">
                                <span class="eyebrow eyebrow-mute">{{ __('site.contact.email') }}</span>
                                <input type="email" name="email" value="{{ old('email') }}" maxlength="160"
                                       class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                                @error('email') <span class="body-s mt-2 block text-clay">{{ $message }}</span> @enderror
                            </label>

                            <label class="block">
                                <span class="eyebrow eyebrow-mute">{{ __('site.contact.branch') }}</span>
                                <select name="branch_id"
                                        class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                                    <option value="">{{ __('site.contact.branch_any') }}</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="block sm:col-span-2">
                                <span class="eyebrow eyebrow-mute">{{ __('site.contact.subject') }}</span>
                                <input type="text" name="subject" value="{{ old('subject', $konu) }}" maxlength="160"
                                       class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                            </label>

                            <label class="block sm:col-span-2">
                                <span class="eyebrow eyebrow-mute">{{ __('site.contact.message') }} *</span>
                                <textarea name="message" rows="5" required maxlength="4000"
                                          class="mt-3 w-full resize-y border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">{{ old('message') }}</textarea>
                                @error('message') <span class="body-s mt-2 block text-clay">{{ $message }}</span> @enderror
                            </label>

                            <div class="sm:col-span-2">
                                <button type="submit" class="btn" data-magnetic="0.2">
                                    {{ __('site.contact.send') }}
                                    <svg viewBox="0 0 16 16" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                        <path d="M2 8h11M9 4l4 4-4 4"/>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </section>

</x-layout>
