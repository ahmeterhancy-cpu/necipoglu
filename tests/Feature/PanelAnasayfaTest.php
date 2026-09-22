<?php

namespace Tests\Feature;

use App\Filament\Widgets\HizliIslemler;
use App\Filament\Widgets\OzetKartlari;
use App\Filament\Widgets\SiteDurumu;
use App\Filament\Widgets\SonMesajlar;
use App\Filament\Widgets\YayinHazirligi;
use App\Models\ContactMessage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PanelAnasayfaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('site:kur')->assertSuccessful();

        $u = new User(['name' => 'Ahmet Erhan', 'email' => 'a@necipoglu.com', 'password' => 'Sifre-2026-xx']);
        $u->forceFill(['is_admin' => true])->save();
        $this->actingAs($u);
    }

    public function test_acilis_sayfasi_acilir_ve_selamlar(): void
    {
        $this->get('/admin')
            ->assertOk()
            ->assertSee('Ahmet')
            ->assertDontSee('Hoş geldin');
    }

    public function test_bos_veritabaninda_tum_bilesenler_calisir(): void
    {
        Livewire::test(OzetKartlari::class)->assertOk()->assertSee('Okunmamış mesaj')->assertSee('Hepsi okundu')->assertSee('Henüz ürün eklenmedi');
        Livewire::test(SonMesajlar::class)->assertOk()->assertSee('Henüz mesaj yok');
        Livewire::test(SiteDurumu::class)->assertOk()->assertSee('Yapım aşamasında')->assertSee('Yok');
        Livewire::test(YayinHazirligi::class)->assertOk()->assertSee('Katalogda henüz ürün yok')->assertSee('0 / 9')->assertDontSee('1 / 9');
        Livewire::test(HizliIslemler::class)->assertOk()->assertSee('Yönetici ekle');
    }

    public function test_mesaj_ve_ayar_degisince_bilesenler_guncellenir(): void
    {
        ContactMessage::create(['name' => 'Ayşe Müşteri', 'phone' => '+90 542 000 0000', 'subject' => 'Duravit lavabo', 'message' => 'Fiyat?']);
        ContactMessage::create(['name' => 'Okunmuş', 'message' => 'x', 'read_at' => now()]);
        Setting::put('construction_enabled', false);
        Setting::put('social_instagram', 'https://instagram.com/necipoglu');

        Livewire::test(OzetKartlari::class)->assertSee('Yanıt bekliyor')->assertSee('2 mesaj');
        Livewire::test(SonMesajlar::class)->assertSee('Ayşe Müşteri')->assertSee('Duravit lavabo')->assertSee('Yeni');
        Livewire::test(SiteDurumu::class)->assertSee('Yayında')->assertDontSee("Önizleme PIN'i");
        Livewire::test(YayinHazirligi::class)->assertSee('1 hesap bağlı');
    }
}
