<x-filament-widgets::widget>
    <x-filament::section heading="Site durumu" icon="heroicon-o-globe-alt">
        <div style="display:grid; gap:1rem">

            {{-- Ana durum: tek bakışta "yayında mı?" --}}
            <div style="display:flex; align-items:center; gap:.75rem; padding:1rem; border-radius:.75rem; background:{{ $yapim ? 'rgba(245,158,11,.10)' : 'rgba(34,197,94,.10)' }}">
                <span style="width:.625rem; height:.625rem; border-radius:999px; flex-shrink:0; background:{{ $yapim ? '#F59E0B' : '#22C55E' }}; box-shadow:0 0 0 4px {{ $yapim ? 'rgba(245,158,11,.20)' : 'rgba(34,197,94,.20)' }}"></span>
                <div>
                    <p style="font-weight:600; line-height:1.3">{{ $yapim ? 'Yapım aşamasında' : 'Yayında' }}</p>
                    <p style="font-size:.8125rem; opacity:.7">
                        {{ $yapim ? 'Ziyaretçiler "Yeni sitemiz hazırlanıyor" sayfasını görüyor.' : 'Site herkese açık.' }}
                    </p>
                </div>
            </div>

            <dl style="display:grid; gap:.625rem; font-size:.875rem">
                @if ($yapim)
                    <div style="display:flex; justify-content:space-between; gap:1rem">
                        <dt style="opacity:.7">Önizleme PIN'i</dt>
                        <dd>
                            <x-filament::badge :color="$pin ? 'success' : 'danger'" size="sm">
                                {{ $pin ? 'Tanımlı' : 'Yok' }}
                            </x-filament::badge>
                        </dd>
                    </div>
                @endif

                <div style="display:flex; justify-content:space-between; gap:1rem">
                    <dt style="opacity:.7">Satış</dt>
                    <dd>
                        <x-filament::badge color="gray" size="sm">
                            {{ $katalog ? 'Katalog modu' : 'Online sipariş açık' }}
                        </x-filament::badge>
                    </dd>
                </div>
            </dl>

            <div style="display:flex; flex-wrap:wrap; gap:.5rem">
                <x-filament::button tag="a" :href="$siteUrl" target="_blank" icon="heroicon-m-arrow-top-right-on-square" size="sm">
                    Siteyi aç
                </x-filament::button>

                <x-filament::button tag="a" :href="$ayarlarUrl" color="gray" outlined icon="heroicon-m-cog-6-tooth" size="sm">
                    Yapım aşaması ayarı
                </x-filament::button>
            </div>

            @if ($yapim && ! $pin)
                <p style="font-size:.8125rem; opacity:.7">
                    PIN tanımlanmadıkça siteyi yalnızca panele giriş yapmış yöneticiler görebilir.
                </p>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
