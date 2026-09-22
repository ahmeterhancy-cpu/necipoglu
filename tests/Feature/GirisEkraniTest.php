<?php

namespace Tests\Feature;

use App\Filament\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GirisEkraniTest extends TestCase
{
    use RefreshDatabase;

    public function test_giris_ekrani_marka_kimligiyle_acilir(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Yönetim paneli')
            ->assertSee('brand/logo-dark.png', escape: false)   // marka logosu
            ->assertSee('fi-simple-layout', escape: false)      // stil bloğu
            ->assertSee('necipoglu.com');
    }

    public function test_dogru_bilgiyle_giris_yapilir_yanlista_hata_verir(): void
    {
        $u = new User(['name' => 'Yönetici', 'email' => 'a@necipoglu.com', 'password' => 'Sifre-2026-xx']);
        $u->forceFill(['is_admin' => true])->save();

        Livewire::test(Login::class)
            ->fillForm(['email' => 'a@necipoglu.com', 'password' => 'yanlis-sifre'])
            ->call('authenticate')
            ->assertHasFormErrors();

        $this->assertGuest();

        Livewire::test(Login::class)
            ->fillForm(['email' => 'a@necipoglu.com', 'password' => 'Sifre-2026-xx'])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($u);
    }

    public function test_yonetici_olmayan_panele_giremez(): void
    {
        $musteri = User::create(['name' => 'Müşteri', 'email' => 'm@ornek.com', 'password' => 'Sifre-2026-xx']);

        $this->actingAs($musteri)->get('/admin')->assertForbidden();
    }
}
