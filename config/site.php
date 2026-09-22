<?php

return [
    /*
    |---------------------------------------------------------------------------
    | Kurulum bilgileri (.env)
    |---------------------------------------------------------------------------
    | env() yalnızca config/ içinde okunur: canlıda `artisan optimize` config'i
    | önbelleğe alınca Laravel .env'i hiç yüklemez, başka yerdeki env() null
    | döner. Komutlar bu değerleri config('site.admin…') ile okur.
    */
    'admin' => [
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
        'name' => env('ADMIN_NAME', 'Yönetici'),
    ],

    // İlk kurulumda yapım aşaması sayfası bu PIN ile açık gelir (site:kur).
    'construction_pin' => env('CONSTRUCTION_PIN'),

    // Grubun kafesi — menüde İştiraklerimiz'in yerini aldı, dış siteye gider.
    // Panelden (Site Ayarları > dura_coffee_url) değiştirilebilir; bu yedek.
    'dura_coffee' => 'https://duracoffee.com.tr/',

    /*
    |---------------------------------------------------------------------------
    | Diller
    |---------------------------------------------------------------------------
    | Varsayılan dil kök dizinde (/), diğerleri kendi öneklerinde servis edilir.
    */
    'default_locale' => 'tr',

    'locales' => [
        'tr' => ['name' => 'Türkçe', 'short' => 'TR', 'html' => 'tr'],
        'en' => ['name' => 'English', 'short' => 'EN', 'html' => 'en'],
    ],

    /*
    |---------------------------------------------------------------------------
    | Kurumsal sabitler
    |---------------------------------------------------------------------------
    | Şubeler, telefonlar ve sosyal hesaplar yönetim panelinden düzenlenir;
    | burada yalnızca panel boşken kullanılacak ilk değerler durur.
    */
    'company' => [
        'legal_name' => 'Cahit Necipoğlu Ltd.',
        'short_name' => 'Cahit Necipoğlu',
        'founded' => 1985,
        'email' => 'info@necipoglu.com',
        'whatsapp' => '+905428550026',
    ],
];
