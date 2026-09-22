<x-filament-widgets::widget>
    <x-filament::section heading="Hızlı işlemler" icon="heroicon-o-bolt">
        <div style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:.625rem">
            @foreach ($islemler as [$ad, $ikon, $url])
                <a href="{{ $url }}"
                   style="display:flex; flex-direction:column; gap:.625rem; padding:.875rem; border-radius:.75rem; border:1px solid rgba(120,120,130,.22); transition:border-color .15s, background .15s; {{ $loop->last && $loop->count % 2 ? 'grid-column:span 2' : '' }}"
                   onmouseover="this.style.background='rgba(120,120,130,.07)';this.style.borderColor='rgba(120,120,130,.45)'"
                   onmouseout="this.style.background='transparent';this.style.borderColor='rgba(120,120,130,.22)'">
                    <x-filament::icon :icon="$ikon" style="width:1.375rem; height:1.375rem; opacity:.75" />
                    <span style="font-size:.875rem; font-weight:500">{{ $ad }}</span>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
