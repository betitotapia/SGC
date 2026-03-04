<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_plans', function (Blueprint $table) {
            $table->id();

            $table->string('folio', 100)->unique();
            $table->string('process')->nullable();
            $table->string('finding_type')->nullable();
            $table->text('finding');
            $table->text('activity')->nullable();
            $table->text('root_cause')->nullable();
            $table->string('department')->nullable();

            // responsable (texto y opcional FK a users)
            $table->string('owner_name')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();

            $table->date('commitment_date')->nullable();
            $table->date('close_date')->nullable();

            // manual
            $table->string('status', 100)->default('ABIERTO');

            // automático
            $table->unsignedTinyInteger('progress')->default(0); // 0..100

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['status']);
            $table->index(['department']);
            $table->index(['owner_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_plans');
    }
};
