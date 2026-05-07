<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table->string('folio', 30)->unique(); // Ej: PRO-RH-001

            $table->string('title');

            $table->enum('type', [
                'procedure',       // Procedimiento
                'process',         // Proceso
                'format',          // Formato
                'work_instruction' // Instrucción de Trabajo
            ]);

            $table->foreignId('department_id')
                ->constrained('departments')
                ->restrictOnDelete();

            // Apunta a la versión actualmente publicada (null hasta el primer publish)
            $table->unsignedBigInteger('current_version_id')->nullable();

            $table->enum('status', [
                'draft',       // Borrador
                'in_review',   // En revisión (firmante 1)
                'in_approval', // En aprobación (firmantes 2 y 3)
                'published',   // Publicado y vigente
                'obsolete',    // Obsoleto (reemplazado por nueva versión)
            ])->default('draft');

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
