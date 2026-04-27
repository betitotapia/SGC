<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiscal_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('rfc', 13)->unique();
            $table->string('legal_name')->comment('Razón social / Nombre legal (tal cual CSF)');
            $table->string('commercial_name')->nullable();
            $table->string('tax_regime_code', 10)->nullable()->comment('c_RegimenFiscal SAT');
            $table->string('tax_regime_name')->nullable();
            $table->string('cfdi_use', 10)->nullable()->comment('Uso CFDI default, ej. G03');
            $table->string('zip', 10)->nullable();
            $table->string('street')->nullable();
            $table->string('exterior_number', 20)->nullable();
            $table->string('interior_number', 20)->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('municipality')->nullable();
            $table->string('state')->nullable();
            $table->string('country', 3)->default('MEX');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('csf_file_path')->nullable()->comment('PDF original almacenado');
            $table->json('csf_raw')->nullable()->comment('Datos originales extraídos del PDF');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiscal_profiles');
    }
};
