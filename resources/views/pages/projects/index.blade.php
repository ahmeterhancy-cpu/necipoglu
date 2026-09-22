@use('App\Support\Locale')

<x-layout :title="__('site.nav.projects')">

    <x-page-hero :eyebrow="__('site.home.projects_eyebrow')"
                 :title="__('site.home.projects_title')" />

    <section class="surface-paper pb-[clamp(5rem,12vh,10rem)]">
        <div class="shell">
            @if ($projects->isEmpty())
                <x-empty-state />
            @else
                <div class="grid gap-x-[clamp(1.5rem,3vw,2.5rem)] gap-y-[clamp(3rem,6vw,5rem)] md:grid-cols-2">
                    @foreach ($projects as $project)
                        <a href="{{ Locale::route('projects.show', $project) }}"
                           class="group block {{ $loop->odd ? '' : 'md:mt-[clamp(2rem,8vh,6rem)]' }}"
                           data-cursor="{{ __('site.common.view') }}">

                            <div class="shot ar-wide">
                                @if ($project->cover)
                                    <img src="{{ Storage::url($project->cover) }}" alt="{{ $project->title }}" loading="lazy" class="desaturate">
                                @else
                                    <div class="placeholder h-full w-full">
                                        <span class="sr-only">{{ $project->title }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-baseline justify-between gap-6 pt-6">
                                <h2 class="h4">{{ $project->title }}</h2>
                                <span class="eyebrow eyebrow-mute shrink-0 tnum">{{ $project->year }}</span>
                            </div>

                            <p class="list-note mt-2">{{ collect([$project->client, $project->location])->filter()->implode(' · ') }}</p>

                            @if ($project->summary)
                                <p class="body-s mute mt-4 max-w-[52ch]">{{ $project->summary }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

</x-layout>
