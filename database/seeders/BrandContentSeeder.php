<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

/**
 * Marka tanıtım metinleri.
 *
 * Her metin markanın KENDİ sitesinden ya da doğrulanabilir kaynaklardan
 * derlendi. Kaynağı bulunamayan markalar bilerek dışarıda bırakıldı —
 * uydurma metin yazmaktansa alan boş kalsın.
 *
 * 'site' anahtarı yalnızca HTTP 200 döndüğü doğrulanan adresler için var;
 * doğrulanamayan adres yazılmadı, çünkü kırık bir "Marka sitesi" düğmesi
 * hiç düğme olmamasından kötüdür.
 */
class BrandContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->icerik() as $slug => $veri) {
            $marka = Brand::where('slug', $slug)->first();

            if (! $marka) {
                $this->command?->warn("Marka bulunamadı: {$slug}");
                continue;
            }

            $marka->description = ['tr' => $veri['tr'], 'en' => $veri['en']];

            if (isset($veri['site'])) {
                $marka->website = $veri['site'];
            }

            if (isset($veri['gruplar'])) {
                $marka->highlights = [
                    'tr' => $veri['gruplar']['tr'],
                    'en' => $veri['gruplar']['en'],
                ];
            }

            $marka->save();
        }
    }

    /** @return array<string, array{tr: string, en: string, site?: string, gruplar?: array{tr: list<string>, en: list<string>}}> */
    protected function icerik(): array
    {
        return [

            'hansgrohe' => [
                'gruplar' => [
                    'tr' => ['El duşları', 'Tepe duşları', 'Duş kolonları', 'Termostatik duş sistemleri', 'Banyo bataryaları', 'Mutfak bataryaları', 'Ankastre setler', 'Duş gideri'],
                    'en' => ['Hand showers', 'Overhead showers', 'Shower pipes', 'Thermostatic shower systems', 'Bathroom mixers', 'Kitchen mixers', 'Concealed sets', 'Shower drains'],
                ],
                'site' => 'https://www.hansgrohe.com.tr',
                'tr' => 'Alman armatür üreticisi hansgrohe 1901’den bu yana duş sistemleri, banyo ve mutfak bataryaları üretiyor. Ürün gamının merkezinde el duşları, tepe duşları, duş kolonları ve ankastre çözümler var; su tasarrufu teknolojileri markanın kendi geliştirdiği başlıklarla sağlanıyor.',
                'en' => 'German fittings manufacturer hansgrohe has been making shower systems and bathroom and kitchen mixers since 1901. Hand showers, overhead showers, shower pipes and concealed solutions sit at the centre of the range, with water-saving technologies built into the brand’s own spray heads.',
            ],

            'axor' => [
                'gruplar' => [
                    'tr' => ['Lavabo bataryaları', 'Küvet ve duş bataryaları', 'Duş sistemleri', 'Mutfak bataryaları', 'Banyo aksesuarları', 'Tasarımcı koleksiyonları'],
                    'en' => ['Basin mixers', 'Bath and shower mixers', 'Shower systems', 'Kitchen mixers', 'Bathroom accessories', 'Designer collections'],
                ],
                'site' => 'https://www.axor-design.com',
                'tr' => 'AXOR, hansgrohe bünyesinde tasarım odaklı banyo ve mutfak koleksiyonlarını yürüten markadır. Armatür, duş, aksesuar ve gider ürünlerini Philippe Starck, Antonio Citterio, Jean-Marie Massaud ve Patricia Urquiola gibi tasarımcılarla birlikte geliştiriyor.',
                'en' => 'AXOR is the design-led bathroom and kitchen arm within hansgrohe. Its taps, showers, accessories and waste systems are developed with designers including Philippe Starck, Antonio Citterio, Jean-Marie Massaud and Patricia Urquiola.',
            ],

            'duravit' => [
                'gruplar' => [
                    'tr' => ['Lavabo', 'Klozet ve bide', 'Küvet', 'Duş teknesi', 'Banyo mobilyası', 'Armatür', 'Duş sistemleri', 'Banyo aksesuarları'],
                    'en' => ['Washbasins', 'Toilets and bidets', 'Bathtubs', 'Shower trays', 'Bathroom furniture', 'Fittings', 'Shower systems', 'Bathroom accessories'],
                ],
                'site' => 'https://www.duravit.com.tr',
                'tr' => 'Geçmişi 1817’ye uzanan Duravit; lavabo, klozet, bide ve küvet gibi banyo seramikleriyle birlikte banyo mobilyası, armatür ve duş sistemleri üretiyor. Koleksiyonlarının önemli bölümü Philippe Starck ve Cecilie Manz gibi tasarımcılarla ortak geliştiriliyor.',
                'en' => 'With a history going back to 1817, Duravit makes bathroom ceramics — washbasins, toilets, bidets and bathtubs — alongside bathroom furniture, fittings and shower systems. A large part of the range is developed with designers such as Philippe Starck and Cecilie Manz.',
            ],

            'geberit' => [
                'gruplar' => [
                    'tr' => ['Gömme rezervuar', 'Kumanda panelleri', 'Montaj sistemleri', 'Klozet ve pisuvar', 'Lavabo grubu', 'Duş çözümleri', 'Banyo mobilyası', 'Tesisat ve tahliye boruları'],
                    'en' => ['Concealed cisterns', 'Flush plates', 'Installation systems', 'Toilets and urinals', 'Washbasin range', 'Shower solutions', 'Bathroom furniture', 'Supply and drainage piping'],
                ],
                'site' => 'https://www.geberit.com.tr',
                'tr' => 'Geberit’in 150 yılı aşkın birikimi duvarın arkasında toplanıyor: gömme rezervuarlar, montaj sistemleri, tahliye ve tesisat boru sistemleri. Bunların yanında klozet, pisuvar, lavabo, duş çözümleri ve banyo mobilyası da üretiyor.',
                'en' => 'Geberit’s expertise of more than 150 years sits behind the wall: concealed cisterns, installation systems, drainage and supply piping. Alongside these it produces toilets, urinals, washbasins, shower solutions and bathroom furniture.',
            ],

            'aco' => [
                'gruplar' => [
                    'tr' => ['Duş kanalları', 'Süzgeç ve ızgaralar', 'Lineer drenaj kanalları', 'Polimer beton kanallar', 'Yağ ayırıcılar', 'Yağmur suyu hasat sistemleri'],
                    'en' => ['Shower channels', 'Gratings and covers', 'Linear drainage channels', 'Polymer concrete channels', 'Oil separators', 'Rainwater harvesting systems'],
                ],
                'site' => 'https://www.aco.com.tr',
                'tr' => 'ACO su yönetimi üzerine uzmanlaşmış bir üretici. Banyo tarafında duş kanalları ve süzgeçler, yapı tarafında ise polimer beton ve plastik lineer drenaj kanalları, yağ ayırıcılar ve yağmur suyu hasat sistemleri üretiyor.',
                'en' => 'ACO specialises in water management. On the bathroom side it makes shower channels and gratings; on the building side, linear drainage channels in polymer concrete and plastic, oil separators and rainwater harvesting systems.',
            ],

            'decor-walther' => [
                'gruplar' => [
                    'tr' => ['Ayna ve makyaj aynası', 'Banyo aydınlatması', 'Sabunluk ve dispenser', 'Havluluk', 'Saklama kapları', 'Duş grubu aksesuarları'],
                    'en' => ['Mirrors and cosmetic mirrors', 'Bathroom lighting', 'Soap dishes and dispensers', 'Towel holders', 'Storage containers', 'Shower accessories'],
                ],
                'site' => 'https://www.decor-walther.com',
                'tr' => '1973’ten bu yana üreten Alman markası Decor Walther, banyo aksesuarı ve aydınlatmada çalışıyor. Ayna, makyaj aynası, aplik, sabunluk, havluluk ve saklama kaplarını krom, pirinç ve mat bitişlerle sunuyor; merkezi Frankfurt.',
                'en' => 'Producing since 1973, the German brand Decor Walther works in bathroom accessories and lighting. Mirrors, cosmetic mirrors, wall lights, soap dispensers, towel holders and storage containers come in chrome, brass and matt finishes; the company is based in Frankfurt.',
            ],

            'gedy' => [
                'gruplar' => [
                    'tr' => ['Duvara monte aksesuarlar', 'Tezgah üstü aksesuarlar', 'Klozet grubu', 'Duş grubu', 'Ayna ve LED aydınlatma', 'Banyo paspası', 'Çamaşır sepeti ve düzenleyici'],
                    'en' => ['Wall-mounted accessories', 'Countertop accessories', 'WC area', 'Shower area', 'Mirrors and LED lighting', 'Bath rugs', 'Laundry bins and organisers'],
                ],
                'site' => 'https://www.gedy.com',
                'tr' => 'İtalyan banyo aksesuarı üreticisi Gedy, 1953’ten bu yana Varese’nin Origgio ilçesinde üretiyor. Duvara monte ve tezgah üstü aksesuarlar, klozet ve duş grubu ürünleri ile tamamlayıcı dekoratif parçalardan oluşan beş binden fazla ürünü var.',
                'en' => 'Italian bathroom accessory maker Gedy has produced in Origgio, Varese since 1953. Its catalogue runs to more than five thousand products: wall-mounted and countertop accessories, WC and shower items, and decorative complements.',
            ],

            'windisch' => [
                'gruplar' => [
                    'tr' => ['Sabunluk ve dispenser', 'Havluluk', 'Makyaj aynası', 'Çöp kovası', 'Kristal detaylı seriler', 'Altın kaplama bitişler'],
                    'en' => ['Soap dishes and dispensers', 'Towel holders', 'Cosmetic mirrors', 'Waste bins', 'Crystal-detailed series', 'Gold-plated finishes'],
                ],
                'tr' => '1934’ten bu yana Barcelona yakınlarındaki Barberà del Vallès’te üreten Windisch, pirinci işleyerek banyo aksesuarı yapıyor. Sabunluk, havluluk, ayna ve çöp kovalarını krom, altın kaplama ve Swarovski kristalli bitişlerle sunuyor.',
                'en' => 'Producing in Barberà del Vallès near Barcelona since 1934, Windisch works brass into bathroom accessories. Soap dispensers, towel holders, mirrors and waste bins come in chrome, gold-plated and Swarovski crystal finishes.',
            ],

            'sonia' => [
                'gruplar' => [
                    'tr' => ['Banyo aksesuarları', 'Banyo mobilyası', 'Ayna ve büyüteçli ayna', 'Armatür', 'Paslanmaz çelik donanım', 'Projeye özel ölçü (Sonia LAB)'],
                    'en' => ['Bathroom accessories', 'Bathroom furniture', 'Mirrors and magnifying mirrors', 'Taps', 'Stainless steel equipment', 'Made-to-measure for projects (Sonia LAB)'],
                ],
                'site' => 'https://soniabath.com',
                'tr' => 'İspanyol üretici Sonia; banyo aksesuarı, mobilya, ayna ve armatür üretiyor. Valencia yakınlarındaki Bonrepós i Mirambell’de bulunan firma, Sonia LAB hattıyla konut ve otel projelerine özel ölçü ve bitişte çözüm de sunuyor.',
                'en' => 'Spanish manufacturer Sonia makes bathroom accessories, furniture, mirrors and fittings. Based in Bonrepós i Mirambell near Valencia, it also offers made-to-measure sizes and finishes for residential and hospitality projects through its Sonia LAB service.',
            ],

            'keraben' => [
                'gruplar' => [
                    'tr' => ['Porselen yer karosu', 'Duvar karosu', 'Büyük ebat karo', 'Dış mekân karosu'],
                    'en' => ['Porcelain floor tiles', 'Wall tiles', 'Large format tiles', 'Outdoor tiles'],
                ],
                'site' => 'https://www.keraben.com',
                'tr' => 'İspanya’nın Castellón bölgesindeki Nules’te 1974’te Gres de Nules adıyla üretime başlayan Keraben, porselen yer karosu ve duvar karosu üretiyor. Bugün Keraben Grupo çatısı altında yüzden fazla ülkeye ürün gönderiyor.',
                'en' => 'Founded in 1974 as Gres de Nules in Nules, Castellón, Keraben produces porcelain floor tiles and wall tiles. Today it ships to more than a hundred countries under the Keraben Grupo umbrella.',
            ],

            'ibero' => [
                'gruplar' => [
                    'tr' => ['Porselen karo', 'Duvar kaplaması', 'Cephe kaplaması', 'Büyük ebat yüzeyler'],
                    'en' => ['Porcelain tiles', 'Wall cladding', 'Façade cladding', 'Large format surfaces'],
                ],
                'site' => 'https://www.iberoceramics.com',
                'tr' => 'Ibero, 2020’de Casainfinita ile Ibero Porcelánico’nun birleşmesiyle bugünkü halini aldı; kökeni Keraben Grupo’nun Nules’teki üretimine dayanıyor. Zemin, duvar ve cephe uygulamaları için porselen karo üretiyor.',
                'en' => 'Ibero took its current form in 2020 through the merger of Casainfinita and Ibero Porcelánico, with roots in Keraben Grupo’s production in Nules. It makes porcelain tiles for floors, walls and façades.',
            ],

            'grespania' => [
                'gruplar' => [
                    'tr' => ['Porselen yer karosu', 'Beyaz hamurlu duvar karosu', 'Coverlam büyük ebat levha', 'Tezgah yüzeyleri', 'Dış mekân karosu', 'Havalandırmalı cephe'],
                    'en' => ['Porcelain floor tiles', 'White body wall tiles', 'Coverlam large format slabs', 'Countertop surfaces', 'Outdoor tiles', 'Ventilated façades'],
                ],
                'site' => 'https://www.grespania.com',
                'tr' => '1976’da kurulan Grespania, Castellón’daki üç fabrikasında porselen yer karosu, beyaz hamurlu duvar karosu ve büyük ebat porselen levha üretiyor. 2010’da tanıttığı Coverlam hattıyla 120×360 cm’ye kadar ince kesitli levhalar çıkarıyor; tezgah, cephe ve iç mekân kaplamasında kullanılıyor.',
                'en' => 'Founded in 1976, Grespania produces porcelain floor tiles, white body wall tiles and large format porcelain slabs across three factories in Castellón. Its Coverlam line, introduced in 2010, reaches sizes up to 120×360 cm in thin sections, used for worktops, façades and interior surfaces.',
            ],

            'dune' => [
                'gruplar' => [
                    'tr' => ['Porselen yer karosu', 'Duvar karosu', 'Seramik ve cam mozaik', 'Dekoratif lavabo', 'SPC zemin kaplaması', 'Özel form karolar'],
                    'en' => ['Porcelain floor tiles', 'Wall tiles', 'Ceramic and glass mosaics', 'Decorative basins', 'SPC rigid core flooring', 'Shaped tiles'],
                ],
                'site' => 'https://www.duneceramics.com',
                'tr' => 'Castellón’un San Juan de Moró ilçesinde üreten Dune, zemin ve duvar için porselen karo, seramik ve cam mozaik üretiyor. Mermer, çimento ve ahşap dokulu koleksiyonlarının yanında dekoratif lavabo ve SPC zemin kaplaması da var.',
                'en' => 'Producing in San Juan de Moró, Castellón, Dune makes porcelain tiles, ceramic and glass mosaics for floors and walls. Alongside marble, cement and wood-effect collections it also offers decorative basins and SPC rigid core flooring.',
            ],

            'el-molino' => [
                'gruplar' => [
                    'tr' => ['Porselen karo', 'Duvar kaplaması', 'Cephe kaplaması', 'Doğal taş ve mermer desenli seriler', 'Ahşap ve çimento desenli seriler', 'Dış mekân'],
                    'en' => ['Porcelain tiles', 'Wall cladding', 'Façade cladding', 'Stone and marble-effect series', 'Wood and cement-effect series', 'Outdoor'],
                ],
                'site' => 'https://www.elmolino.es',
                'tr' => 'Castellón’un Onda ilçesinde üreten El Molino, doğal taş, ahşap, mermer, çimento ve metal dokularından esinlenen porselen karo koleksiyonları çıkarıyor. Mat, parlak, saten ve metalik bitişleriyle iç mekân, dış mekân ve cephe uygulamalarına ürün veriyor.',
                'en' => 'Producing in Onda, Castellón, El Molino develops porcelain tile collections inspired by natural stone, wood, marble, cement and metal. Matt, gloss, satin and metallic finishes cover interior, exterior and façade applications.',
            ],

            'alaplana' => [
                'gruplar' => [
                    'tr' => ['Beyaz hamurlu porselen karo', 'Renkli hamurlu porselen karo', 'Duvar karosu', 'Büyük ebat levha', 'Dış mekân karosu'],
                    'en' => ['White body porcelain tiles', 'Coloured body porcelain tiles', 'Wall tiles', 'Large format slabs', 'Outdoor tiles'],
                ],
                'site' => 'https://nuevaalaplana.es',
                'tr' => 'İspanya’nın Chilches kasabasında üreten Alaplana; beyaz ve renkli hamurlu porselen karo, duvar karosu ve büyük ebat levha üretiyor. Koleksiyonları konut, ticari mekân ve dış mekân kullanımına yönelik.',
                'en' => 'Producing in Chilches, Spain, Alaplana makes white body and coloured body porcelain tiles, wall tiles and large format slabs. Its collections are aimed at residential, commercial and outdoor use.',
            ],

            'platera' => [
                'gruplar' => [
                    'tr' => ['Porselen yer karosu', 'Beyaz hamurlu duvar karosu', 'Zemin kaplaması', 'Duvar kaplaması'],
                    'en' => ['Porcelain floor tiles', 'White body wall tiles', 'Floor coverings', 'Wall coverings'],
                ],
                'tr' => 'Yarım asrı aşkın süredir Castellón bölgesinde üreten La Platera, porselen yer karosu ve beyaz hamurlu duvar karosunda çalışıyor. Ürünleri zemin ve duvar kaplaması olarak konut ve ticari projelerde kullanılıyor.',
                'en' => 'Producing in the Castellón region for over half a century, La Platera works in porcelain floor tiles and white body wall tiles, used as floor and wall coverings in residential and commercial projects.',
            ],

            'ng-kutahya-seramik' => [
                'gruplar' => [
                    'tr' => ['Yer karosu', 'Duvar karosu', 'Porselen karo', 'Ahşap ve mermer desenli seriler', 'NG Stone', 'NG Slim', 'Dış mekân uygulamaları'],
                    'en' => ['Floor tiles', 'Wall tiles', 'Porcelain tiles', 'Wood and marble-effect series', 'NG Stone', 'NG Slim', 'Exterior applications'],
                ],
                'site' => 'https://www.ngkutahyaseramik.com.tr',
                'tr' => 'Kütahya’da üretim yapan NG Kütahya Seramik; ahşap, mermer, taş ve çimento dokularında seramik ve porselen karo üretiyor. NG Stone ve NG Slim gibi hatlarıyla iç ve dış mekân uygulamalarına farklı ebat ve yüzeylerde ürün veriyor.',
                'en' => 'Manufacturing in Kütahya, NG Kütahya Seramik produces ceramic and porcelain tiles in wood, marble, stone and cement textures. Lines such as NG Stone and NG Slim cover interior and exterior applications across a range of sizes and surfaces.',
            ],

            'anka-seramik' => [
                'gruplar' => [
                    'tr' => ['Yer karosu', 'Duvar karosu', 'Porselen karo', 'Fayans'],
                    'en' => ['Floor tiles', 'Wall tiles', 'Porcelain tiles', 'Glazed tiles'],
                ],
                'site' => 'https://www.ankaseramik.com.tr',
                'tr' => 'Anka Seramik, 2007’den bu yana Eskişehir’in Çifteler ilçesindeki fabrikasında yer ve duvar karosu üretiyor. Yıllık on milyon metrekareye yaklaşan kapasitesiyle seramik ve porselen karoda iç pazara ve ihracata çalışıyor.',
                'en' => 'Anka Seramik has produced floor and wall tiles at its plant in Çifteler, Eskişehir since 2007. With an annual capacity approaching ten million square metres, it supplies ceramic and porcelain tiles to the domestic market and for export.',
            ],

            'sanacryl' => [
                'gruplar' => [
                    'tr' => ['Bağımsız küvet', 'Ankastre küvet', 'Duş teknesi', 'Solid duş zemini', 'Hidromasaj sistemleri', 'Kompakt duş ünitesi', 'Akrilik lavabo', 'Outdoor spa'],
                    'en' => ['Freestanding bathtubs', 'Built-in bathtubs', 'Shower trays', 'Solid shower floors', 'Hydromassage systems', 'Compact shower units', 'Acrylic basins', 'Outdoor spa'],
                ],
                'site' => 'http://www.sanacryl.com',
                'tr' => 'Akplast Grubu bünyesindeki Sanacryl, dökme akrilik levhayı vakumla şekillendirerek ıslak hacim ürünleri üretiyor. Bağımsız ve ankastre küvet, monoblok ve panelli duş teknesi, hidromasaj sistemleri, kompakt duş üniteleri, solid duş zeminleri ve akrilik lavabo ürün gamında yer alıyor.',
                'en' => 'Part of the Akplast group, Sanacryl vacuum-forms cast acrylic sheet into wet area products. The range covers freestanding and built-in bathtubs, monobloc and panelled shower trays, hydromassage systems, compact shower units, solid shower floors and acrylic basins.',
            ],

            'smanni' => [
                'gruplar' => [
                    'tr' => ['Lavabo', 'Klozet', 'Pisuvar', 'Renkli vitrifiye', 'Banyo mobilyası'],
                    'en' => ['Washbasins', 'Toilets', 'Urinals', 'Coloured sanitaryware', 'Bathroom furniture'],
                ],
                'site' => 'https://smanni.com',
                'tr' => '2012’de kurulan Smanni, vitrifiye ürünler ve banyo mobilyası üretiyor. Lavabo, klozet ve pisuvarın yanında renkli vitrifiye seçenekleri ve bunlarla eşleşen dolap grupları sunuyor.',
                'en' => 'Founded in 2012, Smanni produces sanitaryware and bathroom furniture. Alongside washbasins, toilets and urinals it offers coloured sanitaryware options and matching cabinet groups.',
            ],

            'teska' => [
                'gruplar' => [
                    'tr' => ['Banyo bataryaları', 'Duş armatürleri', 'Lavabo bataryaları', 'Mutfak eviye bataryaları', 'Duş setleri'],
                    'en' => ['Bathroom mixers', 'Shower fittings', 'Basin mixers', 'Kitchen sink mixers', 'Shower sets'],
                ],
                'site' => 'https://www.teska.com.tr',
                'tr' => 'Üretime Adana’da başlayan ve 2012’de merkezini İstanbul’a taşıyan Teska; banyo ve duş armatürleri, lavabo bataryaları ve mutfak eviye bataryaları üretiyor. Çeyrek asra yaklaşan bir üretim geçmişi var.',
                'en' => 'Teska began manufacturing in Adana and moved its headquarters to Istanbul in 2012. It produces bathroom and shower fittings, basin mixers and kitchen sink mixers, with close to a quarter century of production behind it.',
            ],

            'denko' => [
                'gruplar' => [
                    'tr' => ['Banyo dolabı', 'Boy dolabı', 'Ayna ve aynalı dolap', 'Lavabo grubu', 'Banyo aksesuarları'],
                    'en' => ['Bathroom cabinets', 'Tall units', 'Mirrors and mirror cabinets', 'Basin units', 'Bathroom accessories'],
                ],
                'site' => 'https://denkobanyo.com.tr',
                'tr' => '2001’de Zonguldak’ta kurulan Denko, banyo mobilyası ve aksesuarı üretiyor. Dolap, ayna ve lavabo gruplarını Dream ve Platin gibi serilerde topluyor.',
                'en' => 'Founded in Zonguldak in 2001, Denko produces bathroom furniture and accessories, grouping cabinets, mirrors and basin units into series such as Dream and Platin.',
            ],

            'eva-banyo' => [
                'gruplar' => [
                    'tr' => ['Lavabo', 'Klozet ve monoblok takım', 'Batarya', 'Banyo mobilyası', 'Banyo aksesuarları', 'Mozaik ve bordür'],
                    'en' => ['Washbasins', 'Toilets and close-coupled sets', 'Mixers', 'Bathroom furniture', 'Bathroom accessories', 'Mosaics and borders'],
                ],
                'tr' => 'Eva Banyo; lavabo, klozet, monoblok takım, batarya ve banyo aksesuarları üreten bir Türk markası. Banyo mobilyasının yanında mozaik ve bordür gibi tamamlayıcı ürünler de sunuyor.',
                'en' => 'Eva Banyo is a Turkish brand producing washbasins, toilets, close-coupled sets, mixers and bathroom accessories, alongside bathroom furniture and complementary products such as mosaics and borders.',
            ],

            'cypdus' => [
                'gruplar' => [
                    'tr' => ['Duşakabin', 'Duş teknesi', 'Banyo dolabı', 'Banyo malzemeleri'],
                    'en' => ['Shower enclosures', 'Shower trays', 'Bathroom cabinets', 'Bathroom materials'],
                ],
                'tr' => 'Cypdus, Gazimağusa merkezli bir Kuzey Kıbrıs üreticisi. Yirmi yılı aşkın deneyimiyle duşakabin ve banyo dolabı üretiyor; kendi üretiminin yanında banyo malzemesi tedariki de yapıyor.',
                'en' => 'Cypdus is a Northern Cyprus manufacturer based in Famagusta. With over twenty years of experience it produces shower enclosures and bathroom cabinets, and also supplies bathroom materials alongside its own production.',
            ],

        ];
    }
}
