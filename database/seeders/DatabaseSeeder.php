<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
<<<<<<< HEAD
=======
        // 1. Akun Admin Dinkes Surabaya
        User::updateOrCreate(
            ['email' => 'admin@dinkes.surabaya.go.id'],
            [
                'name' => 'Admin Dinkes Surabaya',
                'password' => bcrypt('password'),
                'role' => 'dinkes',
            ]
        );
>>>>>>> 97a7321 (feat(dinkes): verifikasi dokumen uji lab dan aktivasi badge resmi SLHS (SCRUM-67))

        // Depot Owner (Mitra)
        $depotUser = User::updateOrCreate(
            ['email' => 'mitra@galonku.com'],
            [
                'name' => 'Budi Pemilik Depot',
                'password' => bcrypt('password'),
                'role' => 'depot',
            ]
        );

        // Warga / Consumer
        User::updateOrCreate(
            ['email' => 'warga@example.com'],
            [
                'name' => 'Warga Surabaya',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]
        );

        $this->call([
            DepotBatchSeeder::class,
        ]);
    }
}
