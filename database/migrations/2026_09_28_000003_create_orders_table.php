<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // e.g. MINUM-2609-001
            $table->string('queue_number'); // e.g. #A-08
            $table->foreignId('depot_id')->constrained('depots')->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->foreignId('water_product_id')->nullable()->constrained('water_products')->nullOnDelete();
            $table->string('water_product_name');
            $table->string('water_type')->default('ro');
            $table->unsignedInteger('unit_price');
            $table->unsignedInteger('quantity')->default(1);
            $table->enum('gallon_option', ['bring_own', 'new_gallon'])->default('bring_own');
            $table->unsignedInteger('gallon_fee')->default(0);
            $table->unsignedInteger('total_amount');
            $table->string('pickup_time_estimated');
            $table->string('pickup_pin', 6);
            $table->enum('status', ['DITERIMA', 'SEDANG_DIISI', 'SIAP_DIAMBIL', 'SELESAI', 'BATAL'])->default('DITERIMA');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};