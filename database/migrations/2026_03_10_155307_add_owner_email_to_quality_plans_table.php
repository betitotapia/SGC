<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quality_plans', function (Blueprint $table) {
            $table->string('owner_email', 255)->nullable()->after('owner_name');
        });
    }

    public function down(): void
    {
        Schema::table('quality_plans', function (Blueprint $table) {
            $table->dropColumn('owner_email');
        });
    }
};