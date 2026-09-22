@use('App\Support\Locale')

@php
    $schema = [
        \App\Support\Schema::project($project),
        \App\Support\Schema::breadcrumbs([
            ['name' => __('site.nav.projects'), 'url' => Locale::route('projects.index')],
            ['name' => $project->title, 'url' => url()->current()],
        ]),
    ];
@endphp

<x-layout :title="$project->title" :description="$project->summary" :schema="$schema">

    <section class="surface-paper pt-[clamp(7rem,15vh,10rem)]">
        <div class="shell">
            <a href="{{ Locale::route('projects.index') }}" class="eyebrow eyebrow-mute link" data-reveal="fade">
                ← {{ __('site.home.projects_eyebrow') }}
            </a>

            <h1 class="h2 mt-8 max-w-[16ch]" data-split>{{ $project->title }}</h1>

            <dl class="mt-12 grid grid-cols-2 gap-y-8 md:grid-cols-4" data-reveal-stagger="0.07">
                @foreach ([
                    __('site.contact.subject') => $project->client,
                    __('site.common.branches') => $project->location,
                    __('site.common.since') => $project->year,
                    __('site.home.range_eyebrow') => $project->scope,
                ] as $label => $value)
                    @if ($value)
                        <div>
                            <dt class="eyebrow eyebrow-mute">{{ $label }}</dt>
                            <dd class="body-m mt-3">{{ is_array($value) ? implode(', ', $value) : $value }}</dd>
                        </div>
                    @endif
                @endforeach
            </dl>
        </div>

        <div class="shell mt-[clamp(3rem,8vh,5rem)]">
            <div class="shot ar-wide">
                @if ($project->cover)
                    <img src="{{ Storage::url($project->cover) }}" alt="{{ $project->title }}" fetchpriority="high">
                @else
                    <div class="placeholder h-full w-full"></div>
                @endif
            </div>
        </div>
    </section>

    @if ($project->body)
        <section class="surface-paper band">
            <div class="shell-narrow">
                <div class="body-m mute whitespace-pre-line text-[1.0625rem] leading-[1.75]" data-reveal>{{ $project->body }}</div>
            </div>
        </section>
    @endif

    @if ($gallery = $project->gallery)
        <section class="surface-paper band">
            <div class="shell">
                <div class="grid gap-[clamp(1rem,2vw,2rem)] md:grid-cols-2">
                    @foreach ($gallery as $image)
                        <div class="shot ar-wide {{ $loop->index % 3 === 0 ? 'md:col-span-2' : '' }}">
                            <img src="{{ Storage::url($image) }}" alt="{{ $project->title }}" loading="lazy">
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($others->isNotEmpty())
        <section class="surface-paper band">
            <div class="shell">
                <p class="eyebrow" data-reveal="fade">{{ __('site.home.projects_eyebrow') }}</p>

                <div class="mt-10">
                    @foreach ($others as $other)
                        <a href="{{ Locale::route('projects.show', $other) }}" class="list-row" data-reveal="fade">
                            <span>
                                <span class="list-title block">{{ $other->title }}</span>
                                <span class="list-note block">{{ $other->location }}</span>
                            </span>

                            <span class="list-meta tnum">{{ $other->year }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</x-layout>
