@use('App\Support\Locale')

<x-layout :title="__('site.account.login')">

    <section class="surface-paper pt-[calc(var(--topbar)+clamp(3rem,7vw,5.5rem))] pb-[clamp(4rem,10vw,8rem)]">
        <div class="shell-narrow max-w-[32rem]">

            <h1 class="h1" data-split>{{ __('site.account.login') }}</h1>

            <form method="post" action="{{ Locale::route('login.store') }}" class="mt-10 space-y-7" data-reveal>
                @csrf

                <label class="block">
                    <span class="eyebrow eyebrow-mute">{{ __('site.account.email') }}</span>
                    <input type="email" name="email" required autofocus autocomplete="email" value="{{ old('email') }}"
                           class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                    @error('email') <span class="body-s mt-2 block" style="color:var(--color-clay)">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="eyebrow eyebrow-mute">{{ __('site.account.password') }}</span>
                    <input type="password" name="password" required autocomplete="current-password"
                           class="mt-3 w-full border-b border-line bg-transparent pb-3 outline-none transition-colors focus:border-ink">
                    @error('password') <span class="body-s mt-2 block" style="color:var(--color-clay)">{{ $message }}</span> @enderror
                </label>

                <label class="flex items-center gap-3">
                    <input type="checkbox" name="remember" value="1">
                    <span class="body-s">{{ __('site.account.remember') }}</span>
                </label>

                <button type="submit" class="btn w-full justify-center">{{ __('site.account.login') }}</button>
            </form>

            <p class="body-s mute mt-8">
                {{ __('site.account.no_account') }}
                <a href="{{ Locale::route('register') }}" class="link">{{ __('site.account.register') }}</a>
            </p>
        </div>
    </section>

</x-layout>
