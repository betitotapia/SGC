<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quality_plans', function (Blueprint $table) {
            if (Schema::hasColumn('quality_plans', 'activity')) {
                $table->dropColumn('activity');
            }

            if (Schema::hasColumn('quality_plans', 'root_cause')) {
                $table->dropColumn('root_cause');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quality_plans', function (Blueprint $table) {
            $table->text('activity')->nullable();
            $table->text('root_cause')->nullable();
        });
    }
};