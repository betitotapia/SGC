<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Los tres firmantes se definen por documento al crearlo o editarlo.
            // elaboro = quien redactó, reviso = personal de calidad, autorizo = dirección.
            $table->foreignId('elaboro_id')
                ->nullable()
                ->after('created_by')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('reviso_id')
                ->nullable()
                ->after('elaboro_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('autorizo_id')
                ->nullable()
                ->after('reviso_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['elaboro_id']);
            $table->dropForeign(['reviso_id']);
            $table->dropForeign(['autorizo_id']);
            $table->dropColumn(['elaboro_id', 'reviso_id', 'autorizo_id']);
        });
    }
};
