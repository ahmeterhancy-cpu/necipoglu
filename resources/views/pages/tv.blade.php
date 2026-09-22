@use('App\Support\Locale')

<x-layout title="CN TV">

    <x-page-hero eyebrow="CN TV" :title="__('site.home.tv_title')" />

    <section class="surface-paper pb-[clamp(5rem,12vh,10rem)]">
        <div class="shell">
            @if ($videos->isEmpty())
                <x-empty-state />
            @else
                {{-- Öne çıkan --}}
                @if ($featured)
                    <div class="shot ar-wide" data-video-player>
                        <button type="button"
                                class="group absolute inset-0 z-10 grid place-items-center"
                                data-video-play="{{ $featured->embedUrl() }}"
                                aria-label="{{ $featured->title }}">
                            @if ($poster = ($featured->poster ? Storage::url($featured->poster) : $featured->posterUrl()))
                                <img src="{{ $poster }}" alt="" class="absolute inset-0 h-full w-full object-cover desaturate">
                            @else
                                <span class="placeholder absolute inset-0"></span>
                            @endif

                            <span class="relative grid h-20 w-20 place-items-center rounded-full border border-paper-2/40 bg-ink-2/55 backdrop-blur-sm transition-transform duration-500 group-hover:scale-110">
                                <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                            </span>
                        </button>
                    </div>

                    <div class="mt-6 flex flex-wrap items-baseline justify-between gap-4">
                        <h2 class="h4">{{ $featured->title }}</h2>
                        <span class="eyebrow eyebrow-mute tnum">{{ $featured->formattedDuration() }}</span>
                    </div>

                    @if ($featured->description)
                        <p class="body-m mute mt-4 max-w-[62ch]">{{ $featured->description }}</p>
                    @endif
                @endif

                {{-- Arşiv --}}
                @php $rest = $videos->reject(fn ($v) => $featured && $v->is($featured)); @endphp

                @if ($rest->isNotEmpty())
                    <hr class="rule mt-[clamp(3rem,8vh,5rem)]" data-draw>

                    <div class="mt-12 grid gap-x-[clamp(1rem,2vw,2rem)] gap-y-[clamp(2rem,4vw,3rem)] sm:grid-cols-2 lg:grid-cols-3"
                         data-reveal-stagger="0.06">
                        @foreach ($rest as $video)
                            <div class="group">
                                <div class="shot ar-wide" data-video-player>
                                    <button type="button"
                                            class="absolute inset-0 z-10 grid place-items-center"
                                            data-video-play="{{ $video->embedUrl() }}"
                                            aria-label="{{ $video->title }}">
                                        @if ($poster = ($video->poster ? Storage::url($video->poster) : $video->posterUrl()))
                                            <img src="{{ $poster }}" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover desaturate">
                                        @else
                                            <span class="placeholder absolute inset-0"></span>
                                        @endif

                                        <span class="relative grid h-14 w-14 place-items-center rounded-full border border-paper-2/40 bg-ink-2/55 backdrop-blur-sm transition-transform duration-500 group-hover:scale-110">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                                        </span>
                                    </button>
                                </div>

                                <div class="flex items-baseline justify-between gap-4 pt-5">
                                    <h3 class="h4 text-[1.125rem]">{{ $video->title }}</h3>
                                    <span class="eyebrow eyebrow-mute shrink-0 tnum">{{ $video->formattedDuration() }}</span>
                                </div>

                                @if ($video->category)
                                    <p class="eyebrow mt-2">{{ $video->category }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </section>

    <x-slot:head>
        <script type="module">
            /* Videolar tıklanana kadar yüklenmez — sayfa açılışında üçüncü
               taraf oynatıcı isteği yapılmaz. */
            document.addEventListener('click', (event) => {
                const button = event.target.closest('[data-video-play]');
                if (!button) return;

                const src = button.dataset.videoPlay;
                if (!src) return;

                const frame = document.createElement('iframe');
                frame.src = src;
                frame.title = button.getAttribute('aria-label') ?? '';
                frame.allow = 'accelerometer; autoplay; encrypted-media; picture-in-picture';
                frame.allowFullscreen = true;
                frame.className = 'absolute inset-0 h-full w-full border-0';

                button.closest('[data-video-player]').appendChild(frame);
                button.remove();
            });
        </script>
    </x-slot:head>

</x-layout>
