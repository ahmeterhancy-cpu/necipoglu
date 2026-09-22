<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Yönetici hesabını .env'deki bilgilerden açar.
 *
 * Paylaşımlı sunucuda SSH/Terminal olmayabiliyor; `tinker` ile hesap açmak
 * mümkün değil. Bu komut deploy görevleri arasında çalışır ve FİKİRSİZDİR:
 * hesap zaten varsa hiçbir şey yapmaz, parolayı da ezmez. Yani her deploy'da
 * güvenle koşabilir.
 */
class CreateAdminUser extends Command
{
    protected $signature = 'admin:olustur
        {--eposta= : .env yerine burada verilen adres}
        {--parola= : .env yerine burada verilen parola}
        {--ad= : Görünen ad}';

    protected $description = 'ADMIN_EMAIL / ADMIN_PASSWORD ile yönetici hesabı açar (hesap varsa dokunmaz)';

    public function handle(): int
    {
        // config üzerinden okunuyor, env() ile değil: config önbelleğe
        // alındığında Laravel .env'i hiç yüklemez ve env() null döner.
        $email = $this->option('eposta') ?: config('site.admin.email');
        $password = $this->option('parola') ?: config('site.admin.password');
        $name = $this->option('ad') ?: config('site.admin.name', 'Yönetici');

        if (blank($email) || blank($password)) {
            $this->warn('ADMIN_EMAIL ve ADMIN_PASSWORD boş — hesap açılmadı.');

            // Deploy zinciri kırılmasın: bu adım isteğe bağlı.
            return self::SUCCESS;
        }

        if (User::where('email', $email)->exists()) {
            $this->info($email.' zaten var, dokunulmadı.');

            return self::SUCCESS;
        }

        if (mb_strlen((string) $password) < 10) {
            $this->error('Parola en az 10 karakter olmalı. Hesap açılmadı.');

            return self::FAILURE;
        }

        // is_admin toplu atamaya kapalı (Fillable listesinde yok) — bilerek.
        $user = new User(['name' => $name, 'email' => $email, 'password' => $password]);
        $user->forceFill(['is_admin' => true, 'email_verified_at' => now()])->save();

        $this->info($email.' yönetici olarak açıldı.');
        $this->warn('Panele girdikten sonra .env içindeki ADMIN_PASSWORD satırını silin.');

        return self::SUCCESS;
    }
}
