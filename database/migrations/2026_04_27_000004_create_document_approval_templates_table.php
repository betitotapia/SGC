<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Define quién firma por default para cada tipo de documento en cada departamento.
        // Al crear una nueva versión se copian estas plantillas a document_approvals.
        Schema::create('document_approval_templates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('department_id')
                ->constrained('departments')
                ->cascadeOnDelete();

            // null = aplica a cualquier tipo de documento en ese departamento
            $table->enum('document_type', [
                'procedure',
                'process',
                'format',
                'work_instruction',
            ])->nullable();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('role_in_approval', [
                'author',
                'reviewer',
                'approver',
            ]);

            $table->unsignedTinyInteger('order');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_approval_templates');
    }
};
