<?php

namespace Tests\Feature;

use App\Http\Middleware\ConstructionGate;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Canlıdaki ilk kurulumun aynısı: boş veritabanı + `site:kur`, ardından
 * yapım aşaması kapısı ve PIN akışı.
 */
class KurulumTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('site:kur')->assertSuccessful();
    }

    public function test_ilk_kurulumda_yapim_asamasi_sayfasi_acik_gelir(): void
    {
        $this->get('/')
            ->assertStatus(503)
            ->assertSee('Yeni sitemiz hazırlanıyor.')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

        $this->get('/en/about')->assertStatus(503)->assertSee('Our new website is on its way.');
    }

    public function test_yapim_asamasi_kapatilinca_site_acilir(): void
    {
        Setting::put('construction_enabled', false);

        $this->get('/')->assertOk()->assertSee('CN Elit');
        $this->get('/en/about')->assertOk();
        $this->get('/markalar/dura-bagno')->assertOk()->assertSee('Dura Bagno');
        $this->get('/sepet')->assertNotFound();
    }

    public function test_dogru_pin_cihaza_izin_verir_pin_degisince_izin_duser(): void
    {
        Setting::put('construction_pin', '4826');

        $this->post('/onizleme-pin', ['pin' => '1111', 'to' => '/'])
            ->assertRedirect('/')
            ->assertCookieMissing(ConstructionGate::COOKIE);

        $cookie = $this->post('/onizleme-pin', ['pin' => '4826', 'to' => '/markalar'])
            ->assertRedirect('/markalar')
            ->getCookie(ConstructionGate::COOKIE);

        $this->withCookie($cookie->getName(), $cookie->getValue())->get('/')->assertOk();

        Setting::put('construction_pin', '9999');

        $this->withCookie($cookie->getName(), $cookie->getValue())->get('/')->assertStatus(503);
    }

    public function test_pin_formu_disari_yonlendirmez(): void
    {
        Setting::put('construction_pin', '4826');

        $this->post('/onizleme-pin', ['pin' => '4826', 'to' => '//evil.com'])->assertRedirect('/');
        $this->post('/onizleme-pin', ['pin' => '4826', 'to' => 'https://evil.com'])->assertRedirect('/');
    }

    public function test_panel_livewire_istekleri_kapiya_takilmaz(): void
    {
        // Filament Livewire'ı rastgele önekle servis ediyor; kapı açıkken bu
        // istekler yapım aşaması sayfasına düşerse paneldeki kaydet çalışmaz.
        $this->assertNotSame(503, $this->post('/livewire-abc123/update')->status());
        $this->get('/admin/login')->assertOk();
    }

    public function test_kurulum_ikinci_kez_calisinca_paneldeki_degisikligi_ezmez(): void
    {
        Setting::put('about_title', ['tr' => 'Panelden yazıldı', 'en' => 'From panel']);

        $this->artisan('site:kur')->expectsOutput('Veritabanı dolu, kurulum atlandı.');

        $this->assertSame('Panelden yazıldı', Setting::get('about_title'));
    }
}
