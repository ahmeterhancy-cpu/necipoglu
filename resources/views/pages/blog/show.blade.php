@use('App\Support\Locale')

@php
    $schema = [
        \App\Support\Schema::article($post),
        \App\Support\Schema::breadcrumbs([
            ['name' => __('site.nav.blog'), 'url' => Locale::route('blog.index')],
            ['name' => $post->title, 'url' => url()->current()],
        ]),
    ];
@endphp

<x-layout :title="$post->title" :description="$post->excerpt" :schema="$schema">

    {{-- Okuma ilerleme çubuğu --}}
    <div class="fixed inset-x-0 top-0 z-[95] h-[2px] origin-left scale-x-0 bg-ink" data-progress aria-hidden="true"></div>

    <article class="surface-paper pt-[clamp(7rem,15vh,10rem)]">
        <div class="shell-narrow">
            <a href="{{ Locale::route('blog.index') }}" class="eyebrow eyebrow-mute link" data-reveal="fade">
                ← {{ __('site.nav.blog') }}
            </a>

            <p class="eyebrow mt-8" data-reveal="fade">
                {{ collect([$post->topic, $post->published_at?->translatedFormat('j F Y')])->filter()->implode(' · ') }}
            </p>

            <h1 class="h2 mt-6" data-split>{{ $post->title }}</h1>

            @if ($post->excerpt)
                <p class="lede mute mt-8 max-w-none" data-reveal>{{ $post->excerpt }}</p>
            @endif
        </div>

        @if ($post->cover)
            <div class="shell mt-[clamp(3rem,8vh,5rem)]">
                <div class="shot ar-wide">
                    <img src="{{ Storage::url($post->cover) }}" alt="{{ $post->title }}" fetchpriority="high">
                </div>
            </div>
        @endif

        <div class="shell-narrow band">
            <div class="body-m mute whitespace-pre-line text-[1.0625rem] leading-[1.8]" data-reveal>{{ $post->body }}</div>

            <hr class="rule mt-12" data-draw>

            <p class="eyebrow eyebrow-mute mt-8">{{ $post->author }}</p>
        </div>
    </article>

    @if ($others->isNotEmpty())
        <section class="surface-paper band">
            <div class="shell">
                <p class="eyebrow" data-reveal="fade">{{ __('site.nav.blog') }}</p>

                <div class="mt-10">
                    @foreach ($others as $other)
                        <a href="{{ Locale::route('blog.show', $other) }}" class="list-row" data-reveal="fade">
                            <span class="list-meta tnum">{{ $other->published_at?->format('d.m.y') }}</span>
                            <span class="list-title min-w-0">{{ $other->title }}</span>
                            <span class="eyebrow eyebrow-mute hidden md:block">{{ $other->topic }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</x-layout>
