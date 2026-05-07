<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('elaboro_cargo', 150)->nullable()->after('elaboro_id');
            $table->string('reviso_cargo',  150)->nullable()->after('reviso_id');
            $table->string('autorizo_cargo', 150)->nullable()->after('autorizo_id');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['elaboro_cargo', 'reviso_cargo', 'autorizo_cargo']);
        });
    }
};
