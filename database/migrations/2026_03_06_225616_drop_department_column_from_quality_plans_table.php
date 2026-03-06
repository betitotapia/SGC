<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quality_plans', function (Blueprint $table) {
            if (Schema::hasColumn('quality_plans', 'department')) {
                $table->dropColumn('department');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quality_plans', function (Blueprint $table) {
            $table->string('department')->nullable();
        });
    }
};