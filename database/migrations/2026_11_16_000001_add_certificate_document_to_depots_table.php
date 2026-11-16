<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('depots', function (Blueprint $table) {
            $table->string('certificate_document_path')->nullable()->after('certification_status');
            $table->text('certificate_notes')->nullable()->after('certificate_document_path');
        });
    }

    public function down(): void
    {
        Schema::table('depots', function (Blueprint $table) {
            $table->dropColumn(['certificate_document_path', 'certificate_notes']);
        });
    }
};
