<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quality_plan_monitorings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('plan_id')
                ->constrained('quality_plans')
                ->cascadeOnDelete();

            $table->string('period', 100)->nullable();
            $table->text('activity_to_monitor');
            $table->string('responsible_name', 255)->nullable();

            $table->boolean('is_effective')->nullable();

            $table->text('target_goal')->nullable();
            $table->unsignedTinyInteger('goal_percentage')->nullable();

            $table->date('action_close_date')->nullable();
            $table->text('final_result')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_plan_monitorings');
    }
};
