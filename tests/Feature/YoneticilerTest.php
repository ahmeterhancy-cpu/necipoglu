<?php

namespace Tests\Feature;

use App\Filament\Resources\Admins\Pages\CreateAdmin;
use App\Filament\Resources\Admins\Pages\EditAdmin;
use App\Filament\Resources\Admins\Pages\ListAdmins;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class YoneticilerTest extends TestCase
{
    use RefreshDatabase;

    private function yonetici(string $email, string $sifre = 'EskiSifre-2026'): User
    {
        $u = new User(['name' => $email, 'email' => $email, 'password' => $sifre]);
        $u->forceFill(['is_admin' => true])->save();

        return $u;
    }

    public function test_panelden_ikinci_yonetici_eklenir_ve_panele_girebilir(): void
    {
        $this->actingAs($this->yonetici('ilk@necipoglu.com'));

        Livewire::test(CreateAdmin::class)
            ->fillForm([
                'name' => 'İkinci Yönetici',
                'email' => 'ikinci@necipoglu.com',
                'password' => 'YeniSifre-2026',
                'password_confirmation' => 'YeniSifre-2026',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $yeni = User::where('email', 'ikinci@necipoglu.com')->first();
        $this->assertTrue($yeni->is_admin);
        $this->assertTrue(Hash::check('YeniSifre-2026', $yeni->password));

        auth()->logout();
        $this->actingAs($yeni)->get('/admin')->assertOk();
    }

    public function test_sifre_kisa_ya_da_tekrar_uyusmazsa_kaydedilmez(): void
    {
        $this->actingAs($this->yonetici('ilk@necipoglu.com'));

        Livewire::test(CreateAdmin::class)
            ->fillForm(['name' => 'X', 'email' => 'x@necipoglu.com', 'password' => 'kisa', 'password_confirmation' => 'kisa'])
            ->call('create')
            ->assertHasFormErrors(['password']);

        Livewire::test(CreateAdmin::class)
            ->fillForm(['name' => 'X', 'email' => 'x@necipoglu.com', 'password' => 'YeniSifre-2026', 'password_confirmation' => 'Baska-2026-xx'])
            ->call('create')
            ->assertHasFormErrors(['password']);

        $this->assertDatabaseMissing('users', ['email' => 'x@necipoglu.com']);
    }

    public function test_duzenlemede_bos_sifre_mevcut_sifreyi_korur(): void
    {
        $ben = $this->yonetici('ilk@necipoglu.com');
        $diger = $this->yonetici('diger@necipoglu.com');
        $this->actingAs($ben);

        Livewire::test(EditAdmin::class, ['record' => $diger->getRouteKey()])
            ->fillForm(['name' => 'Yeni Ad', 'password' => '', 'password_confirmation' => ''])
            ->call('save')
            ->assertHasNoFormErrors();

        $diger->refresh();
        $this->assertSame('Yeni Ad', $diger->name);
        $this->assertTrue(Hash::check('EskiSifre-2026', $diger->password));
    }

    public function test_kendi_hesabi_ve_son_yonetici_silinemez_digeri_silinir(): void
    {
        $ben = $this->yonetici('ilk@necipoglu.com');
        $this->actingAs($ben);

        // Tek yönetici: kendi hesabında silme yok.
        Livewire::test(EditAdmin::class, ['record' => $ben->getRouteKey()])
            ->assertActionHidden(DeleteAction::class);

        $diger = $this->yonetici('diger@necipoglu.com');

        Livewire::test(EditAdmin::class, ['record' => $ben->getRouteKey()])
            ->assertActionHidden(DeleteAction::class);

        Livewire::test(EditAdmin::class, ['record' => $diger->getRouteKey()])
            ->assertActionVisible(DeleteAction::class)
            ->callAction(DeleteAction::class);

        $this->assertModelMissing($diger);
    }

    public function test_listede_yalnizca_yoneticiler_gorunur(): void
    {
        $ben = $this->yonetici('ilk@necipoglu.com');
        $musteri = User::create(['name' => 'Müşteri', 'email' => 'musteri@ornek.com', 'password' => 'Musteri-2026']);
        $this->actingAs($ben);

        Livewire::test(ListAdmins::class)
            ->assertCanSeeTableRecords([$ben])
            ->assertCanNotSeeTableRecords([$musteri]);
    }

    public function test_ilk_hesap_komutu_yonetici_varsa_hicbir_sey_yapmaz(): void
    {
        $this->yonetici('ilk@necipoglu.com');
        config(['site.admin.email' => 'env@necipoglu.com', 'site.admin.password' => 'EnvSifre-2026']);

        $this->artisan('admin:olustur')->assertSuccessful();

        $this->assertDatabaseMissing('users', ['email' => 'env@necipoglu.com']);
    }

    public function test_ilk_hesap_komutu_bos_veritabaninda_yonetici_acar(): void
    {
        config(['site.admin.email' => 'env@necipoglu.com', 'site.admin.password' => 'EnvSifre-2026']);

        $this->artisan('admin:olustur')->assertSuccessful();

        $this->assertTrue(User::where('email', 'env@necipoglu.com')->value('is_admin'));
    }
}
