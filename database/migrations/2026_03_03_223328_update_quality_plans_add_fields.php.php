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
        Schema::table('quality_plans', function (Blueprint $table) {
            $table->date('open_date')->nullable()->after('owner_id');

            $table->string('origin', 100)->nullable()->after('open_date');

            // finding_type ya existe como string nullable, lo dejamos (solo cambiaremos a select en el form)
            $table->string('detected_by', 255)->nullable()->after('finding_type');
            $table->string('auditor_type', 20)->nullable()->after('detected_by');

            // Nuevo FK departamento
            $table->foreignId('department_id')->nullable()->after('root_cause')
                ->constrained('departments')->nullOnDelete();

            // Si ya existe columna `department` (texto), la vamos a dejar por ahora
            // para no romper datos; luego la eliminamos cuando migres datos.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('quality_plans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn(['open_date','origin','detected_by','auditor_type']);
        });
    }
};
