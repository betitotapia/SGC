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
        Schema::create('plans', function (Blueprint $table) {
    $table->id();
    $table->string('folio')->unique();
    $table->string('proceso')->nullable();
    $table->string('tipo_hallazgo')->nullable();
    $table->text('hallazgo');
    $table->text('actividad')->nullable();
    $table->text('causa_raiz')->nullable();
    $table->string('departamento')->nullable();
    $table->string('responsable')->nullable();

    $table->date('fecha_compromiso')->nullable();
    $table->date('fecha_cierre')->nullable();

    // estatus “humano”
    $table->string('estatus')->default('ABIERTO'); // ABIERTO / EN_PROCESO / CERRADO
    // progreso calculado
    $table->unsignedTinyInteger('progress')->default(0); // 0..100

    $table->text('observaciones')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
