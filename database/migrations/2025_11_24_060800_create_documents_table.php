<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            // Primary Key - ID RANDOM untuk routing/URL
            $table->string('id')->primary(); // DOCF100001, DOCK200002, DOCB300003

            // Nomor Dokumen (UNIQUE, tapi bukan PK)
            $table->string('doc_number')->unique(); // 22.015/F1-FB/XI/2025

            // Foreign Key ke document_types
            $table->string('doc_type_id');

            // Polymorphic Relation (ke BEFAST data)
            $table->string('documentable_type')->nullable(); // 'Subscription', 'Customer', dll
            $table->string('documentable_id')->nullable(); // ID dari BEFAST

            // Reference Data (cache dari BEFAST untuk display)
            $table->string('subscription_id')->nullable(); // SUB-20231118-001
            $table->string('customer_name')->nullable(); // Agus Supratman (cached)

            // Document Info
            $table->date('document_date'); // Tanggal dokumen
            $table->enum('status', ['generated', 'printed', 'signed', 'cancelled'])
                  ->default('generated');

            // File Storage
            $table->string('file_path')->nullable(); // Path ke S3

            // Notes
            $table->text('notes')->nullable();

            // Audit Trail
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('doc_type_id')
                  ->references('doc_type_id')
                  ->on('document_types')
                  ->onDelete('restrict');

            // Indexes
            $table->index('doc_number'); // Query by nomor dokumen
            $table->index(['documentable_type', 'documentable_id']);
            $table->index(['doc_type_id', 'document_date']);
            $table->index('subscription_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
