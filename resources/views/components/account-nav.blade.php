@use('App\Support\Locale')

<nav class="flex flex-wrap items-center gap-x-8 gap-y-3" aria-label="{{ __('site.account.title') }}">
    @foreach ([
        'account' => __('site.account.title'),
        'account.orders' => __('site.account.orders'),
        'account.addresses' => __('site.account.addresses'),
    ] as $route => $label)
        <a href="{{ Locale::route($route) }}"
           class="eyebrow link {{ request()->routeIs('*'.$route) ? '' : 'eyebrow-mute' }}"
           @if (request()->routeIs('*'.$route)) aria-current="page" @endif>{{ $label }}</a>
    @endforeach

    <form method="post" action="{{ Locale::route('logout') }}" class="ml-auto">
        @csrf
        <button type="submit" class="eyebrow eyebrow-mute link">{{ __('site.account.logout') }}</button>
    </form>
</nav>
