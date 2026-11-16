<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // Contoh: DINKES-SBY-2026-X9K2L1
            $table->foreignId('depot_id')->constrained('depots')->onDelete('cascade');
            $table->string('reporter_name');
            $table->string('reporter_phone');
            $table->string('issue_type'); // keruh, berbau, berasa, lumut, serangga, lainnya
            $table->string('issue_title');
            $table->text('description');
            $table->string('photo_path')->nullable();
            $table->date('purchase_date')->nullable();
            $table->enum('status', ['TERKIRIM_KE_DINKES', 'SEDANG_INVESTIGASI', 'INSPEKSI_LAPANGAN', 'SELESAI'])->default('TERKIRIM_KE_DINKES');
            $table->text('dinkes_notes')->nullable();
            $table->string('proof_image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
