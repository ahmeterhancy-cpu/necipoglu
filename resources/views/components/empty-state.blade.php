@props(['body' => null])

{{-- İçerik henüz eklenmemiş bölümler için sessiz boş durum. --}}
<div {{ $attributes->merge(['class' => 'rounded-md border border-dashed border-line p-[clamp(1.75rem,4vw,3rem)]']) }} data-reveal>
    <p class="body-m">{{ $body ?? __('site.common.empty') }}</p>
</div>
