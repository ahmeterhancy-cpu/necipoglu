@use('App\Support\Locale')

<x-layout :title="__('site.account.register')">

    <section class="surface-paper pt-[calc(var(--topbar)+clamp(3rem,7vw,5.5rem))] pb-[clamp(4rem,10vw,8rem)]">
        <div class="shell-narrow max-w-[32rem]">

            <h1 class="h1" data-split>{{ __('site.account.register') }}</h1>

            <form method="post" action="{{ Locale::route('register.store') }}" class="mt-10 space-y-7" data-reveal>
                @csrf

                <div class="hidden" aria-hidden="true">
                    <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                <label class="block">
                    <span class="eyebrow eyebrow-mute">{{ __('site.account.name') }}</span>
                    <input type="text" name="name" required autofocus autocomplete="name" maxlength="120" value="{{ old('name') }}"
                           class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                    @error('name') <span class="body-s mt-2 block" style="color:var(--color-clay)">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="eyebrow eyebrow-mute">{{ __('site.account.email') }}</span>
                    <input type="email" name="email" required autocomplete="email" maxlength="160" value="{{ old('email') }}"
                           class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                    @error('email') <span class="body-s mt-2 block" style="color:var(--color-clay)">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="eyebrow eyebrow-mute">{{ __('site.account.phone') }}</span>
                    <input type="tel" name="phone" required autocomplete="tel" maxlength="40" value="{{ old('phone') }}"
                           class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                    @error('phone') <span class="body-s mt-2 block" style="color:var(--color-clay)">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="eyebrow eyebrow-mute">{{ __('site.account.password') }}</span>
                    <input type="password" name="password" required autocomplete="new-password"
                           class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                    @error('password') <span class="body-s mt-2 block" style="color:var(--color-clay)">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="eyebrow eyebrow-mute">{{ __('site.account.password_again') }}</span>
                    <input type="password" name="password_confirmation" required autocomplete="new-password"
                           class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                </label>

                <button type="submit" class="btn w-full justify-center">{{ __('site.account.register') }}</button>
            </form>

            <p class="body-s mute mt-8">
                {{ __('site.account.has_account') }}
                <a href="{{ Locale::route('login') }}" class="link">{{ __('site.account.login') }}</a>
            </p>
        </div>
    </section>

</x-layout>
