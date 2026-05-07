<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('document_approvals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('document_version_id')
                ->constrained('document_versions')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('role_in_approval', [
                'author',   // Elaboró
                'reviewer', // Revisó
                'approver', // Autorizó
            ]);

            // Posición en la cadena de firmas (1 = primero en firmar)
            $table->unsignedTinyInteger('order');

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            $table->timestamp('signed_at')->nullable();
            $table->text('comments')->nullable();
            $table->string('ip_address', 45)->nullable(); // IPv4 + IPv6

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_approvals');
    }
};
