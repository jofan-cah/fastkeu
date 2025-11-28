<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->string('doc_type_id')->primary(); // Format: DT-YYYYMMDD-RANDOM6
            $table->string('code')->unique(); // form, konf, ba, dll
            $table->string('name'); // Formulir Berlangganan
            $table->string('prefix')->nullable(); // 22
            $table->string('format_code')->nullable(); // F1-FB

            // PENTING: Counter TIDAK RESET, terus increment!
            $table->unsignedInteger('current_number')->default(0); // Current counter
            $table->string('current_month')->nullable(); // XI (Romawi)
            $table->string('current_year')->nullable(); // 2025

            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
