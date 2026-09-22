<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Yerel geliştirme içindir; canlıda çalıştırılmaz (bkz. site:kur).
        // Parola koda yazılmaz — .env'deki ADMIN_EMAIL / ADMIN_PASSWORD.
        $email = config('site.admin.email');
        $password = config('site.admin.password');

        if (filled($email) && filled($password)) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => config('site.admin.name'),
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                    'is_admin' => true,
                ],
            );
        } else {
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD boş — yönetici hesabı açılmadı.');
        }

        $this->call(CoreSeeder::class);
    }
}
