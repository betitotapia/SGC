<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('document_id')
                ->constrained('documents')
                ->cascadeOnDelete();

            $table->string('version_number', 10); // 1.0, 1.1, 2.0

            // Archivo subido para esta versión
            $table->string('file_path')->nullable();
            $table->string('original_name')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();

            $table->text('change_reason')->nullable(); // Por qué cambió

            $table->date('effective_date')->nullable(); // Fecha de vigencia

            $table->enum('status', [
                'draft',       // En edición
                'in_review',   // Enviada a revisión
                'in_approval', // En proceso de firmas
                'approved',    // Completamente aprobada (publicada)
                'superseded',  // Reemplazada por versión más nueva
            ])->default('draft');

            // Quién envió a revisión y cuándo
            $table->foreignId('submitted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();

            // Cuándo se completaron todas las firmas
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->unique(['document_id', 'version_number']);
        });

        // Ahora podemos agregar la FK de current_version_id en documents
        Schema::table('documents', function (Blueprint $table) {
            $table->foreign('current_version_id')
                ->references('id')
                ->on('document_versions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['current_version_id']);
        });

        Schema::dropIfExists('document_versions');
    }
};
