<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * GERÇEK veri. Şubeler ve telefonlar necipoglu.com'dan alınmıştır.
 * Ürün grupları müşterinin verdiği yelpazedir.
 *
 * Metinler (hero, manifesto vb.) ilk taslaktır — yönetim panelinden
 * düzenlenmek üzere konmuştur.
 */
class CoreSeeder extends Seeder
{
    public function run(): void
    {
        $this->settings();
        $this->branches();
        $this->categories();
    }

    protected function settings(): void
    {
        $settings = [
            'general' => [
                'site_name' => 'Cahit Necipoğlu',
                'legal_name' => 'Cahit Necipoğlu Ltd.',
                'founded_year' => 1985,
                'email' => 'info@necipoglu.com',
                'whatsapp' => '+905428550026',
            ],

            'home' => [
                'hero_eyebrow' => ['tr' => 'Kuzey Kıbrıs', 'en' => 'Northern Cyprus'],
                'hero_title' => [
                    'tr' => 'Yaşam alanlarının yüzeyini biz kuruyoruz.',
                    'en' => 'We build the surfaces of living spaces.',
                ],
                'hero_lede' => [
                    'tr' => 'Banyo, seramik, mermer ve parkede Kıbrıs’ın güvenilir adresi. Üç şube, tek standart: doğru malzeme, doğru zamanda.',
                    'en' => 'Cyprus’s trusted address for bathroom, ceramic, marble and flooring. Three branches, one standard: the right material, at the right time.',
                ],
                'manifesto_title' => [
                    'tr' => 'Bir yüzey, mekânın karakterini belirler.',
                    'en' => 'A surface defines the character of a space.',
                ],
                'manifesto_body' => [
                    'tr' => 'Seramiğin dokusu, mermerin damarı, armatürün ağırlığı — bunlar detay değil, kararın kendisidir. Kırk yılı aşkın süredir müteahhitlere, mimarlara ve ev sahiplerine bu kararları doğru vermeleri için gereken malzemeyi ve bilgiyi sunuyoruz.',
                    'en' => 'The texture of ceramic, the vein of marble, the weight of a fitting — these are not details, they are the decision itself. For over forty years we have supplied contractors, architects and homeowners with the materials and knowledge to get those decisions right.',
                ],

                'about_title' => [
                    'tr' => 'Kırk yıldır Kıbrıs’ta banyo, seramik, mermer ve parke.',
                    'en' => 'Forty years of bathrooms, tiles, marble and flooring in Cyprus.',
                ],
                'about_body' => [
                    'tr' => 'Kıbrıs inşa eder, Cahit Necipoğlu tamamlar.',
                    'en' => 'Cyprus builds. Cahit Necipoğlu finishes.',
                ],
                'vision_body' => [
                    'tr' => 'Kuzey Kıbrıs’ta bir yüzey malzemesi alınacağı zaman akla gelen ilk adres olmak. Ürünü rafa dizmekle yetinmeyen, hangi mekâna neyin uyduğunu bilen ve bu bilgiyi paylaşan bir tedarikçi olarak kalmak.',
                    'en' => 'To be the first address that comes to mind in Northern Cyprus when a surface material is needed. To remain a supplier that does more than stock a shelf — one that knows which material suits which space, and shares that knowledge.',
                ],
                'mission_body' => [
                    'tr' => 'Müteahhide, mimara ve ev sahibine aynı özenle yaklaşmak; bütçesi ne olursa olsun herkese doğru malzemeyi, gerçek stok bilgisiyle ve zamanında vermek. Satıştan sonra da telefonun açık kalması.',
                    'en' => 'To treat contractors, architects and homeowners with the same care; to give everyone the right material with honest stock information and on time, whatever the budget. And to still answer the phone after the sale.',
                ],

                'market_image' => 'media/market.jpg',
                'market_title' => [
                    'tr' => 'Şantiyenin geri kalanı burada.',
                    'en' => 'Everything else the site needs.',
                ],
                'market_body' => [
                    'tr' => 'Kırk yıldır malzeme veriyoruz; burası o işin market hâli.',
                    'en' => 'Forty years of supplying materials — this is that trade over the counter.',
                ],
                'market_intro' => [
                    'tr' => 'Bir vida için Lefkoşa’ya inmek zorunda kalmayın. Şantiyede işi durduran çoğu şey küçük bir parçadır — o parçayı burada bulursunuz.',
                    'en' => 'No need to drive into town for a single screw. Most of what stops work on a site is a small part — you will find it here.',
                ],
                'market_groups' => [
                    'tr' => "Hırdavat ve el aletleri
Boya ve yardımcı ürünler
Elektrik malzemeleri
Tesisat malzemeleri
Yapı kimyasalları
Bahçe ve dış mekân",
                    'en' => "Hardware and hand tools
Paint and finishing products
Electrical supplies
Plumbing supplies
Construction chemicals
Garden and outdoor",
                ],
            ],

            'contact' => [
                'social_instagram' => '',
                'social_facebook' => '',
                'social_youtube' => '',
                'social_linkedin' => '',
            ],

            'seo' => [
                'meta_title' => [
                    'tr' => 'Cahit Necipoğlu — Banyo, Seramik, Mermer ve Parke | Kuzey Kıbrıs',
                    'en' => 'Cahit Necipoğlu — Bathroom, Ceramic, Marble & Flooring | Northern Cyprus',
                ],
                'meta_description' => [
                    'tr' => 'Banyo, tuvalet, duşakabin, seramik, mermer ve parke ürünlerinde Kıbrıs’ın güvenilir adresi. CN Lefkoşa, CN Elit ve CN Gazimağusa.',
                    'en' => 'Cyprus’s trusted address for bathroom, toilet, shower enclosure, ceramic, marble and flooring products. CN Nicosia, CN Elit and CN Famagusta.',
                ],
            ],
        ];

        foreach ($settings as $group => $entries) {
            foreach ($entries as $key => $value) {
                Setting::updateOrCreate(['key' => $key], ['group' => $group, 'value' => $value]);
            }
        }
    }

    protected function branches(): void
    {
        $branches = [
            [
                'slug' => 'lefkosa',
                'name' => ['tr' => 'CN Lefkoşa', 'en' => 'CN Nicosia'],
                'kind' => ['tr' => 'Merkez', 'en' => 'Head office'],
                'address' => [
                    'tr' => 'Şht. Ecvet Yusuf Caddesi No. 37, Yenişehir, Lefkoşa',
                    'en' => 'Şht. Ecvet Yusuf Street No. 37, Yenişehir, Nicosia',
                ],
                'city' => 'Lefkoşa',
                // Kesin konum müşterinin verdiği Google Haritalar iğnesi.
                'lat' => 35.1896553,
                'lng' => 33.3577681,
                'phones' => ['+90 542 855 0026', '+90 542 872 1907'],
                'whatsapp' => '+905428550026',
                'position' => 1,
            ],
            [
                'slug' => 'degirmenlik',
                'name' => ['tr' => 'CN Elit', 'en' => 'CN Elit'],
                'kind' => ['tr' => 'Showroom', 'en' => 'Showroom'],
                'address' => [
                    'tr' => 'Dr. Fazıl Küçük Caddesi No. 6, Değirmenlik',
                    'en' => 'Dr. Fazıl Küçük Street No. 6, Değirmenlik',
                ],
                'city' => 'Değirmenlik',
                // Adres metni Gazimağusa'daki aynı adlı bulvara düşüyordu; kesin
                // konum müşterinin verdiği Google Haritalar iğnesi.
                'lat' => 35.22132,
                'lng' => 33.489096,
                'phones' => ['+90 542 856 0026', '+90 548 860 0025'],
                'whatsapp' => '+905428560026',
                'position' => 2,
            ],
            [
                'slug' => 'gazimagusa',
                'name' => ['tr' => 'CN Gazimağusa', 'en' => 'CN Famagusta'],
                'kind' => ['tr' => 'Showroom', 'en' => 'Showroom'],
                'address' => [
                    'tr' => 'Organize Sanayi Bölgesi, 3. Sokak, Tuzla, Gazimağusa',
                    'en' => 'Organised Industrial Zone, 3rd Street, Tuzla, Famagusta',
                ],
                'city' => 'Gazimağusa',
                // Kesin konum müşterinin verdiği Google Haritalar iğnesi.
                'lat' => 35.145334,
                'lng' => 33.903082,
                'phones' => ['+90 546 998 8558', '+90 546 995 8558'],
                'whatsapp' => '+905469988558',
                'position' => 3,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(['slug' => $branch['slug']], $branch);
        }
    }

    protected function categories(): void
    {
        $categories = [
            [
                'slug' => 'banyo',
                'name' => ['tr' => 'Banyo', 'en' => 'Bathroom'],
                'tagline' => [
                    'tr' => 'Lavabo, küvet ve banyo aksesuarları',
                    'en' => 'Basins, bathtubs and bathroom accessories',
                ],
                'description' => [
                    'tr' => 'Ankastre lavabolardan serbest duran küvetlere, armatürden aksesuara kadar banyonun tamamı. Yerinde ölçü ve uygulama desteğiyle.',
                    'en' => 'The complete bathroom, from built-in basins to freestanding tubs, fittings and accessories — with on-site measurement and installation support.',
                ],
            ],
            [
                'slug' => 'tuvalet',
                'name' => ['tr' => 'Tuvalet', 'en' => 'Toilet'],
                'tagline' => [
                    'tr' => 'Klozet ve rezervuar çözümleri',
                    'en' => 'WC pans and cistern solutions',
                ],
                'description' => [
                    'tr' => 'Asma klozetten gömme rezervuara, akıllı klozetten tuvalet aksesuarlarına kadar su tasarrufu odaklı çözümler.',
                    'en' => 'Water-saving solutions from wall-hung pans and concealed cisterns to smart toilets and accessories.',
                ],
            ],
            [
                'slug' => 'dusakabin',
                'name' => ['tr' => 'Duşakabin', 'en' => 'Shower enclosure'],
                'tagline' => [
                    'tr' => 'Cam ve köşe duşakabin modelleri',
                    'en' => 'Glass and corner shower enclosure models',
                ],
                'description' => [
                    'tr' => 'Temperli cam, ölçüye özel üretim ve köşe/niş modelleriyle her banyo planına oturan duş çözümleri.',
                    'en' => 'Tempered glass, made-to-measure production and corner or alcove models that fit any bathroom plan.',
                ],
            ],
            [
                'slug' => 'seramik',
                'name' => ['tr' => 'Seramik', 'en' => 'Ceramic'],
                'tagline' => [
                    'tr' => 'Fayans ve karo seçenekleri',
                    'en' => 'Wall and floor tile options',
                ],
                'description' => [
                    'tr' => 'Büyük ebat porselen karodan desenli fayansa, iç ve dış mekân için sürtünme sınıfına uygun geniş bir seramik arşivi.',
                    'en' => 'A broad ceramic archive from large-format porcelain to patterned wall tiles, rated for indoor and outdoor use.',
                ],
            ],
            [
                'slug' => 'mermer',
                'name' => ['tr' => 'Mermer', 'en' => 'Marble'],
                'tagline' => [
                    'tr' => 'Doğal taş ve granit ürünleri',
                    'en' => 'Natural stone and granite',
                ],
                'description' => [
                    'tr' => 'Tezgâh, merdiven, denizlik ve döşeme için doğal mermer ve granit; blok seçiminden yerinde montaja kadar.',
                    'en' => 'Natural marble and granite for worktops, stairs, sills and flooring — from block selection to on-site installation.',
                ],
            ],
            [
                'slug' => 'parke',
                'name' => ['tr' => 'Parke', 'en' => 'Flooring'],
                'tagline' => [
                    'tr' => 'Laminat ve masif parke seçenekleri',
                    'en' => 'Laminate and solid wood flooring',
                ],
                'description' => [
                    'tr' => 'Yüksek trafiğe dayanıklı laminat parkeden masif ve lamine ahşaba, süpürgelik ve profil tamamlayıcılarıyla birlikte.',
                    'en' => 'From heavy-traffic laminate to solid and engineered wood, complete with skirting and trim profiles.',
                ],
            ],
        ];

        foreach ($categories as $i => $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category + ['position' => $i + 1, 'is_active' => true, 'show_on_home' => true],
            );
        }
    }
}
