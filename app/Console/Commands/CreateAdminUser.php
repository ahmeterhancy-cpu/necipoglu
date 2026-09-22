<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * İLK yönetici hesabını .env'deki bilgilerden açar.
 *
 * Yöneticiler veritabanında (`users.is_admin`) durur ve panelden (Kurumsal >
 * Yöneticiler) yönetilir. Ama panele girecek ilk hesap bir yerden gelmeli:
 * sunucuda SSH/Terminal yok, `tinker` çalıştırılamıyor. Bu komut deploy
 * görevleri arasında koşar ve YALNIZCA HİÇ YÖNETİCİ YOKKEN iş yapar; bir
 * yönetici oluştuktan sonra .env'deki ADMIN_* satırları tamamen etkisizdir.
 */
class CreateAdminUser extends Command
{
    protected $signature = 'admin:olustur
        {--eposta= : .env yerine burada verilen adres}
        {--parola= : .env yerine burada verilen parola}
        {--ad= : Görünen ad}';

    protected $description = 'Hiç yönetici yoksa ADMIN_EMAIL / ADMIN_PASSWORD ile ilk yöneticiyi açar';

    public function handle(): int
    {
        if (User::where('is_admin', true)->exists()) {
            $this->info('Yönetici var; ilk hesap adımı atlandı (yöneticiler panelden yönetilir).');

            return self::SUCCESS;
        }

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
        $this->warn('Panele girdikten sonra .env içindeki ADMIN_PASSWORD satırını silin; diğer yöneticileri panelden ekleyin.');

        return self::SUCCESS;
    }
}
