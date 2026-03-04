<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('plan_id')
                ->constrained('quality_plans')
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->date('commitment_date')->nullable();

            $table->string('status', 20)->default('ABIERTA');
            $table->timestamp('closed_at')->nullable();

            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['plan_id','status']);
            $table->index(['assignee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_tasks');
    }
};
