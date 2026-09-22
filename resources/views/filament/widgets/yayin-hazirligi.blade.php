<x-filament-widgets::widget>
    <x-filament::section heading="Yayın hazırlığı" icon="heroicon-o-clipboard-document-check"
                         :description="$tamam.' / '.$toplam.' madde tamam'">

        {{-- İlerleme çubuğu — renk panelin birincil (antrasit) tonu. --}}
        <div style="height:.5rem; border-radius:999px; background:rgba(120,120,130,.18); overflow:hidden; margin-bottom:1.25rem">
            <div style="height:100%; width:{{ $yuzde }}%; border-radius:999px; background:var(--primary-600, #475569); transition:width .6s"></div>
        </div>

        <ul style="display:grid; gap:.25rem">
            @foreach ($maddeler as $m)
                <li>
                    <a href="{{ $m['url'] }}"
                       style="display:flex; align-items:center; gap:.875rem; padding:.625rem .75rem; margin:0 -.75rem; border-radius:.625rem; transition:background .15s"
                       onmouseover="this.style.background='rgba(120,120,130,.08)'" onmouseout="this.style.background='transparent'">

                        @if ($m['tamam'])
                            <x-filament::icon icon="heroicon-s-check-circle" style="width:1.375rem; height:1.375rem; color:#22C55E; flex-shrink:0" />
                        @else
                            <span style="width:1.375rem; height:1.375rem; flex-shrink:0; border-radius:999px; border:2px dashed rgba(120,120,130,.55)"></span>
                        @endif

                        <span style="flex:1; min-width:0">
                            <span style="display:block; font-weight:500; font-size:.875rem; {{ $m['tamam'] ? 'opacity:.55' : '' }}">{{ $m['baslik'] }}</span>
                            <span style="display:block; font-size:.8125rem; opacity:.65">{{ $m['detay'] }}</span>
                        </span>

                        <x-filament::icon icon="heroicon-m-chevron-right" style="width:1rem; height:1rem; opacity:.4; flex-shrink:0" />
                    </a>
                </li>
            @endforeach
        </ul>
    </x-filament::section>
</x-filament-widgets::widget>
