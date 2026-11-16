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
        // Jalankan DepotBatchSeeder dulu sebelum membuat akun demo mitra@galonku.com,
        // karena DepotBatchSeeder membersihkan user role 'depot' terlebih dahulu.
        $this->call([
            DepotBatchSeeder::class,
        ]);

        // 1. Akun Admin Dinkes Surabaya
        User::updateOrCreate(
            ['email' => 'admin@dinkes.surabaya.go.id'],
            [
                'name' => 'Admin Dinkes Surabaya',
                'password' => bcrypt('password'),
                'role' => 'dinkes',
            ]
        );

        // 2. Depot Owner (Mitra Demo) - langsung dihubungkan ke Depot #1
        $depotUser = User::updateOrCreate(
            ['email' => 'mitra@galonku.com'],
            [
                'name' => 'Budi Pemilik Depot',
                'password' => bcrypt('password'),
                'role' => 'depot',
            ]
        );

        $firstDepot = \App\Models\Depot::orderBy('id', 'asc')->first();
        if ($firstDepot) {
            $firstDepot->update(['user_id' => $depotUser->id]);
        }

        // 3. Warga / Consumer
        User::updateOrCreate(
            ['email' => 'warga@example.com'],
            [
                'name' => 'Warga Surabaya',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]
        );
    }
}
