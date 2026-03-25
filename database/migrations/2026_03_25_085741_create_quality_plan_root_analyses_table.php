<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quality_plan_root_analyses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('plan_id')
                ->constrained('quality_plans')
                ->cascadeOnDelete();

            $table->text('analysis_description')->nullable();
            $table->longText('analysis_team')->nullable(); // JSON con nombre/cargo
            $table->text('comments')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_plan_root_analyses');
    }
};