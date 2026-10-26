<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('water_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('depot_id')->constrained('depots')->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['ro', 'mineral', 'alkali', 'branded'])->default('ro');
            $table->unsignedInteger('price'); // refill price per gallon
            $table->string('description')->nullable();
            $table->unsignedInteger('tds_avg')->default(18);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('water_products');
    }
};
