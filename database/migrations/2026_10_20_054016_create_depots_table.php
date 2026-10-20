<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->text('address');
            $table->string('district'); // e.g. Gubeng, Sukolilo, Rungkut
            $table->string('city')->default('Surabaya');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->string('phone');
            $table->string('whatsapp');
            $table->string('open_hours')->default('07:00 - 21:00 WIB');
            $table->boolean('is_open')->default(true);
            $table->decimal('rating', 3, 1)->default(5.0);
            $table->unsignedInteger('review_count')->default(0);

            // Sertifikasi Laik Higiene Sanitasi (SLHS) Dinkes
            $table->boolean('is_certified')->default(true);
            $table->string('slhs_number')->nullable();
            $table->string('dinkes_region')->default('Dinas Kesehatan Kota Surabaya');
            $table->string('issued_date')->nullable();
            $table->string('expiry_date')->nullable();
            $table->string('grade')->default('A (Sangat Baik)');
            $table->enum('certification_status', ['AKTIF', 'PROSES_RENEWAL', 'BELUM_TERSERTIFIKASI'])->default('AKTIF');

            // Hasil Uji Lab Air Terakhir
            $table->string('last_tested_date')->nullable();
            $table->string('lab_name')->default('Balai Besar Laboratorium Kesehatan (BBLK) Surabaya');
            $table->unsignedInteger('tds_ppm')->default(18); // PPM
            $table->decimal('ph_level', 3, 1)->default(7.4);
            $table->string('ecoli_status')->default('Negatif (0 CFU/100ml)');
            $table->string('coliform_status')->default('Negatif (0 CFU/100ml)');
            $table->boolean('is_lab_passed')->default(true);

            // Galon Baru Fee
            $table->unsignedInteger('new_gallon_fee')->default(35000);
            $table->json('facilities')->nullable();
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depots');
    }
};
