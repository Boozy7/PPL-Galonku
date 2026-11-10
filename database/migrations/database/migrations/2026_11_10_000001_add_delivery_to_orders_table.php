<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('delivery_type', ['pickup', 'delivery'])->default('pickup')->after('gallon_fee');
            $table->unsignedInteger('delivery_fee')->default(0)->after('delivery_type');
            $table->text('delivery_address')->nullable()->after('delivery_fee');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_type', 'delivery_fee', 'delivery_address']);
        });
    }
};