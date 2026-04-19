<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Jeden administrátorský účet (přihlášení e-mailem z config/admin.php).
     * Heslo v produkci změňte hned po nasazení (nebo nastavte ADMIN_PASSWORD v .env před seedem).
     */
    public function run(): void
    {
        $email = config('admin.email');
        $password = (string) env('ADMIN_PASSWORD', 'Arytmie2026');

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrátor',
                'password' => Hash::make($password),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
