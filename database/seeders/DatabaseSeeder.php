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
