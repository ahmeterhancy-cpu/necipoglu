@use('App\Support\Locale')

<x-layout :title="__('site.nav.subsidiaries')">

    <x-page-hero :eyebrow="__('site.nav.subsidiaries')"
                 :title="app()->getLocale() === 'tr' ? 'Aynı çatı altında, farklı alanlarda.' : 'One group, several fields.'"
                 :lede="app()->getLocale() === 'tr'
                    ? 'Cahit Necipoğlu Ltd. bünyesindeki şirketler; yapı malzemesinden farklı sektörlere uzanan bir grup oluşturur.'
                    : 'The companies within Cahit Necipoğlu Ltd. form a group extending from building materials into other sectors.'" />

    <section class="surface-paper pb-[clamp(5rem,12vh,10rem)]">
        <div class="shell">
            @if ($subsidiaries->isEmpty())
                <x-empty-state />
            @else
                <div>
                    @foreach ($subsidiaries as $subsidiary)
                        <div class="list-row" data-reveal="fade">
                            <span>
                                <span class="list-title block">{{ $subsidiary->name }}</span>
                                <span class="list-note block max-w-[64ch]">
                                    {{ collect([$subsidiary->sector, $subsidiary->tagline])->filter()->implode(' · ') }}
                                </span>
                            </span>

                            <span class="flex shrink-0 items-center gap-6">
                                @if ($subsidiary->founded)
                                    <span class="eyebrow eyebrow-mute tnum">{{ $subsidiary->founded }}</span>
                                @endif

                                @if ($subsidiary->website)
                                    <a href="{{ $subsidiary->website }}" target="_blank" rel="noopener" data-no-veil class="eyebrow link">
                                        {{ __('site.common.view') }}
                                    </a>
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

</x-layout>
