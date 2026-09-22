<?php

namespace App\Filament\Pages;

use App\Filament\Support\Translatable;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Anahtar/değer site ayarları için tek ekran. Kaynak (resource) yerine
 * sayfa kullanıldı: yönetici "ayar kaydı" eklemez, hazır alanları doldurur.
 */
class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Site Ayarları';

    protected static ?string $title = 'Site Ayarları';

    protected static string|\UnitEnum|null $navigationGroup = 'Kurumsal';

    protected static ?int $navigationSort = 9;

    protected string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    /** Hangi ayarın çok dilli olduğu tek yerde tanımlı. */
    protected const TRANSLATABLE = [
        'hero_eyebrow', 'hero_title', 'hero_lede',
        'manifesto_title', 'manifesto_body',
        'vision_body', 'mission_body',
        'about_title', 'about_body',
        'market_title', 'market_body', 'market_intro', 'market_groups',
        'meta_title', 'meta_description',
        'construction_title', 'construction_body',
    ];

    public function mount(): void
    {
        $this->form->fill(Setting::map());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Yapım aşaması')
                    ->description('Açıkken ziyaretçiler siteyi değil "yapım aşamasında" sayfasını görür. O sayfadaki "Yetkili girişi" alanına PIN kodunu giren cihazda site normal açılır; başka hiç kimse göremez. Panele giriş yapmış yönetici siteyi her zaman görür.')
                    ->schema([
                        Toggle::make('construction_enabled')
                            ->label('Yapım aşaması sayfasını göster')
                            ->live(),

                        TextInput::make('construction_pin')
                            ->label('PIN kodu')
                            ->password()
                            ->revealable()
                            ->minLength(4)
                            ->maxLength(12)
                            ->regex('/^\d+$/')
                            ->validationMessages(['regex' => 'PIN yalnızca rakamlardan oluşmalı.'])
                            ->required(fn (Get $get): bool => (bool) $get('construction_enabled'))
                            ->helperText('4–12 haneli, yalnızca rakam. Değiştirdiğinizde daha önce PIN girilmiş bütün cihazların izni düşer.'),

                        Translatable::tabs([
                            'construction_title' => ['label' => 'Başlık (boşsa: "Yeni sitemiz hazırlanıyor.")', 'type' => 'textarea', 'rows' => 2],
                            'construction_body' => ['label' => 'Metin (boşsa varsayılan metin)', 'type' => 'textarea', 'rows' => 3],
                        ]),
                    ]),

                Section::make('Ana sayfa')
                    ->description('Giriş bölümünde ve mekân şeridinde görünen metinler.')
                    ->schema([
                        Translatable::tabs([
                            'hero_eyebrow' => ['label' => 'Üst etiket'],
                            'hero_title' => ['label' => 'Ana başlık', 'type' => 'textarea', 'rows' => 2],
                            'hero_lede' => ['label' => 'Giriş metni', 'type' => 'textarea', 'rows' => 3],
                            'manifesto_title' => ['label' => 'Kurumsal başlık', 'type' => 'textarea', 'rows' => 2],
                            'manifesto_body' => ['label' => 'Kurumsal metin', 'type' => 'textarea', 'rows' => 5],
                        ]),
                    ]),

                Section::make('Hakkımızda sayfası')
                    ->description('Sayfanın açılış başlığı ve giriş metni. Boş bırakılırsa ana sayfadaki kurumsal metin kullanılır.')
                    ->schema([
                        Translatable::tabs([
                            'about_title' => ['label' => 'Açılış başlığı', 'type' => 'textarea', 'rows' => 2],
                            'about_body' => ['label' => 'Giriş metni', 'type' => 'textarea', 'rows' => 4],
                        ]),
                    ]),

                Section::make('Yapı Market')
                    ->description('Yapı Market sayfası ve ana sayfadaki tanıtım şeridi. Boş bırakılan alan sayfada hiç görünmez.')
                    ->schema([
                        Translatable::tabs([
                            'market_title' => ['label' => 'Başlık', 'type' => 'textarea', 'rows' => 2],
                            'market_body' => ['label' => 'Kısa tanıtım', 'type' => 'textarea', 'rows' => 3],
                            'market_intro' => ['label' => 'Uzun metin', 'type' => 'textarea', 'rows' => 5],
                            'market_groups' => ['label' => 'Ürün grupları', 'type' => 'textarea', 'rows' => 6],
                        ]),

                        TextInput::make('market_address')->label('Adres'),
                        TextInput::make('market_phone')->label('Telefon'),
                        TextInput::make('market_hours')->label('Çalışma saatleri'),

                        FileUpload::make('market_image')
                            ->label('Tanıtım fotoğrafı')
                            ->image()->directory('media')->disk('public')
                            ->helperText('Ana sayfadaki şeritte ve sayfa başlığında görünür. Geniş yatay kadraj.'),
                    ]),

                Section::make('Vizyon ve misyon')
                    ->description('Hakkımızda sayfasında yan yana iki sütunda görünür. Boş bırakılan sütun basılmaz.')
                    ->schema([
                        Translatable::tabs([
                            'vision_body' => ['label' => 'Vizyon', 'type' => 'textarea', 'rows' => 4],
                            'mission_body' => ['label' => 'Misyon', 'type' => 'textarea', 'rows' => 4],
                        ]),
                    ]),

                Section::make('Kurumsal bilgiler')
                    ->columns(2)
                    ->schema([
                        TextInput::make('site_name')->label('Site adı'),
                        TextInput::make('legal_name')->label('Ticari unvan'),
                        TextInput::make('founded_year')->label('Kuruluş yılı')->numeric(),
                        TextInput::make('email')->label('E-posta')->email(),
                        TextInput::make('whatsapp')
                            ->label('WhatsApp numarası')
                            ->helperText('Başında + ve ülke kodu ile. Örn: +905428550026'),
                        TextInput::make('dura_coffee_url')
                            ->label('Dura Coffee web adresi')
                            ->url()
                            ->placeholder('https://duracoffee.com.tr/')
                            ->helperText('Site menüsündeki "Dura Coffee" bağlantısı bu adrese gider. Boşsa https://duracoffee.com.tr/ kullanılır.'),
                    ]),

                Section::make('Görseller')
                    ->description('Boş bırakılırsa kurulumla gelen varsayılan fotoğraf kullanılır.')
                    ->schema([
                        FileUpload::make('hero_image')
                            ->label('Ana sayfa giriş fotoğrafı')
                            ->image()->directory('media')->disk('public')
                            ->helperText('Ana sayfanın en üstünde tam ekran görünür. En az 2000 piksel.'),

                        FileUpload::make('scene_image')
                            ->label('Ana sayfa mekân şeridi')
                            ->image()->directory('media')->disk('public')
                            ->helperText('Ana sayfadaki tam genişlik şeritte görünür.'),

                        FileUpload::make('about_image')
                            ->label('Hakkımızda şerit fotoğrafı')
                            ->image()->directory('media')->disk('public')
                            ->helperText('Hakkımızda sayfasındaki tam genişlik şeritte görünür. Geniş yatay kadraj, en az 1920 piksel.'),
                    ]),

                Section::make('Sosyal hesaplar')
                    ->columns(2)
                    ->description('Boş bırakılan hesap sitede hiç görünmez.')
                    ->schema([
                        TextInput::make('social_instagram')->label('Instagram')->url(),
                        TextInput::make('social_facebook')->label('Facebook')->url(),
                        TextInput::make('social_youtube')->label('YouTube')->url(),
                        TextInput::make('social_linkedin')->label('LinkedIn')->url(),
                    ]),

                Section::make('Arama motoru')
                    ->description('Google sonuçlarında görünen başlık ve açıklama.')
                    ->schema([
                        Translatable::tabs([
                            'meta_title' => ['label' => 'Sayfa başlığı'],
                            'meta_description' => ['label' => 'Açıklama', 'type' => 'textarea', 'rows' => 3],
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')->label('Kaydet')->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            // Çok dilli alanlar dizi olarak, diğerleri düz değer olarak yazılır.
            Setting::put($key, $value, in_array($key, self::TRANSLATABLE, true) ? 'home' : 'general');
        }

        Notification::make()->title('Ayarlar kaydedildi.')->success()->send();
    }
}
