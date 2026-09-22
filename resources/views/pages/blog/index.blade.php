@use('App\Support\Locale')

<x-layout :title="__('site.nav.blog')">

    <x-page-hero :eyebrow="__('site.nav.blog')"
                 :title="app()->getLocale() === 'tr' ? 'Malzeme, uygulama ve bakım üzerine.' : 'On material, installation and care.'" />

    <section class="surface-paper pb-[clamp(5rem,12vh,10rem)]">
        <div class="shell">
            @if ($posts->isEmpty())
                <x-empty-state />
            @else
                <div>
                    @foreach ($posts as $post)
                        <a href="{{ Locale::route('blog.show', $post) }}" class="list-row" data-reveal="fade" data-cursor="{{ __('site.common.view') }}">
                            <span class="list-meta tnum">{{ $post->published_at?->format('d.m.y') }}</span>

                            <span class="min-w-0">
                                <span class="list-title block">{{ $post->title }}</span>
                                @if ($post->excerpt)
                                    <span class="list-note mt-2 block max-w-[64ch]">{{ $post->excerpt }}</span>
                                @endif
                            </span>

                            <span class="hidden shrink-0 items-center gap-6 md:flex">
                                @if ($post->topic)
                                    <span class="eyebrow">{{ $post->topic }}</span>
                                @endif
                                @if ($post->read_minutes)
                                    <span class="eyebrow eyebrow-mute tnum">{{ $post->read_minutes }}′</span>
                                @endif
                            </span>
                        </a>
                    @endforeach
                </div>

                <div class="mt-16">{{ $posts->links() }}</div>
            @endif
        </div>
    </section>

</x-layout>
