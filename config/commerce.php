<?php

return [
    /*
    |---------------------------------------------------------------------------
    | Katalog modu
    |---------------------------------------------------------------------------
    | Açıkken site sipariş almaz: sepet, ödeme ve üyelik yolları 404 döner,
    | ürünlerde "Sepete ekle" yerine "İncele" ve "Bilgi al" çıkar. Sipariş
    | altyapısı silinmedi — SHOP_CATALOG=false ile olduğu gibi geri gelir.
    */
    'catalog' => (bool) env('SHOP_CATALOG', true),

    /*
    |---------------------------------------------------------------------------
    | Para birimi
    |---------------------------------------------------------------------------
    | Tüm tutarlar veritabanında KURUŞ cinsinden tam sayı tutulur. Kayan
    | noktalı sayı para hesabına hiç bulaşmaz.
    */
    'currency' => 'TRY',

    /*
    |---------------------------------------------------------------------------
    | Vergi
    |---------------------------------------------------------------------------
    | Fiyatlar KDV dahil gösterilir (perakendede standart). Ayrı bir vergi
    | satırı hesaplanmaz; sipariş özetinde yalnızca bilgi notu çıkar.
    */
    'prices_include_tax' => true,

    /*
    |---------------------------------------------------------------------------
    | Kargo / teslimat yöntemleri
    |---------------------------------------------------------------------------
    | Tutarlar kuruş. free_over: bu tutarın üzerindeki sepetlerde ücretsiz.
    | null ise hiç ücretsiz olmaz.
    */
    'shipping' => [
        'pickup' => [
            'price' => 0,
            'free_over' => null,
            'label' => ['tr' => 'Şubeden teslim alma', 'en' => 'Collect from branch'],
            'note' => [
                'tr' => 'Siparişiniz hazır olduğunda size haber veriyoruz; seçtiğiniz şubeden teslim alıyorsunuz.',
                'en' => 'We notify you when your order is ready for collection from your chosen branch.',
            ],
            'needs_address' => false,
        ],

        'delivery' => [
            'price' => 45000,          // 450,00 ₺
            'free_over' => 2500000,    // 25.000,00 ₺ üzeri ücretsiz
            'label' => ['tr' => 'Adrese teslimat', 'en' => 'Delivery to address'],
            'note' => [
                'tr' => 'KKTC genelinde adrese teslim. Ağır ürünlerde teslimat için sizinle iletişime geçiyoruz.',
                'en' => 'Delivery across Northern Cyprus. For heavy items we contact you to arrange delivery.',
            ],
            'needs_address' => true,
        ],
    ],

    /*
    |---------------------------------------------------------------------------
    | Ödeme yöntemleri
    |---------------------------------------------------------------------------
    | enabled=false olanlar ödeme adımında görünmez. Kart ile ödeme, sağlayıcı
    | kimlik bilgileri girilene kadar kapalı — yarım bir entegrasyonun canlıda
    | görünmesindense hiç görünmemesi doğru.
    */
    'payments' => [
        'transfer' => [
            'enabled' => true,
            'label' => ['tr' => 'Havale / EFT', 'en' => 'Bank transfer'],
            'note' => [
                'tr' => 'Sipariş numaranızı açıklamaya yazarak havale edin. Ödeme görüldüğünde siparişiniz hazırlanır.',
                'en' => 'Transfer with your order number in the reference. We prepare your order once payment clears.',
            ],
        ],

        'on_delivery' => [
            'enabled' => true,
            'label' => ['tr' => 'Teslimatta ödeme', 'en' => 'Pay on delivery'],
            'note' => [
                'tr' => 'Teslim sırasında nakit veya kart ile ödeyebilirsiniz.',
                'en' => 'Pay by cash or card at the time of delivery.',
            ],
        ],

        'card' => [
            'enabled' => false,   // sağlayıcı kimlik bilgileri gelince açılacak
            'label' => ['tr' => 'Kredi kartı', 'en' => 'Credit card'],
            'note' => ['tr' => '', 'en' => ''],
        ],
    ],

    /*
    |---------------------------------------------------------------------------
    | Havale bilgileri
    |---------------------------------------------------------------------------
    | Sipariş sonrası ekranda ve e-postada gösterilir. Gerçek hesap bilgileri
    | girilene kadar boş bırakılmalı — yanlış IBAN göstermek en kötü senaryo.
    */
    'bank_accounts' => [
        // ['bank' => 'Örnek Bankası', 'holder' => 'Cahit Necipoğlu Ltd.', 'iban' => 'TR00 0000 ...'],
    ],

    /*
    |---------------------------------------------------------------------------
    | Sepet
    |---------------------------------------------------------------------------
    */
    'cart' => [
        'max_quantity' => 99,     // kalem başına üst sınır
        'lifetime_days' => 30,    // terk edilmiş sepetlerin temizlenme süresi
    ],
];
