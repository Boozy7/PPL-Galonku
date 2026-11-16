<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('depot_id')->constrained('depots')->onDelete('cascade');
            $table->string('user_name');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->unsignedTinyInteger('clarity_rating')->default(5);
            $table->unsignedTinyInteger('taste_rating')->default(5);
            $table->unsignedTinyInteger('cleanliness_rating')->default(5);
            $table->unsignedTinyInteger('service_rating')->default(5);
            $table->text('comment');
            $table->boolean('is_verified_purchase')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
