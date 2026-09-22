{{-- Giriş ekranının görünümü. Panelde derlenmiş özel bir Tailwind teması
     yok; bu yüzden stil burada, yalnızca giriş sayfasına basılıyor.
     Renkler sitenin jetonlarıyla aynı: mürekkep #383E42, kâğıt #FFF. --}}
<style>
    .fi-simple-layout {
        position: relative;
        background:
            radial-gradient(120% 90% at 50% -10%, #4A5257 0%, rgba(74,82,87,0) 60%),
            #383E42;
    }

    /* İnce ızgara dokusu — sitedeki çizgi diliyle aynı, çok düşük kontrastta. */
    .fi-simple-layout::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,.045) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.045) 1px, transparent 1px);
        background-size: 96px 96px;
        pointer-events: none;
    }

    .fi-simple-main {
        position: relative;
        border: 0;
        border-radius: 1rem;
        box-shadow: 0 24px 60px -24px rgba(0,0,0,.55);
        padding: clamp(1.75rem, 4vw, 2.5rem);
    }

    /* Başlık alanı dikey flex; sitedeki gibi sola hizalı dursun. */
    .fi-simple-header {
        align-items: flex-start;
        text-align: left;
        gap: .25rem;
    }

    .fi-simple-header-heading {
        font-size: 1.5rem;
        letter-spacing: -.01em;
    }

    /* Marka logosu (panel ayarındaki brandLogo) kartın üstünde, büyütülmüş. */
    .fi-simple-header .fi-logo {
        height: 2.25rem !important;
        width: auto;
        margin-bottom: 1.5rem;
    }

    .cn-giris-alt {
        position: relative;
        margin-top: 1.75rem;
        padding-top: 1.25rem;
        border-top: 1px solid rgba(120,120,130,.22);
        display: flex;
        flex-wrap: wrap;
        gap: .75rem 1rem;
        justify-content: space-between;
        align-items: center;
        font-size: .8125rem;
    }

    .cn-giris-alt a { opacity: .7; transition: opacity .15s; }
    .cn-giris-alt a:hover { opacity: 1; }

    .dark .fi-simple-main { box-shadow: 0 24px 60px -24px rgba(0,0,0,.75); }
</style>
