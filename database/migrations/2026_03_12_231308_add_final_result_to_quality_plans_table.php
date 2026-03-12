<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quality_plans', function (Blueprint $table) {
            $table->text('final_result')->nullable()->after('notes');
            $table->unsignedBigInteger('final_result_by')->nullable()->after('final_result');
            $table->timestamp('final_result_at')->nullable()->after('final_result_by');

            $table->foreign('final_result_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quality_plans', function (Blueprint $table) {
            $table->dropForeign(['final_result_by']);
            $table->dropColumn([
                'final_result',
                'final_result_by',
                'final_result_at',
            ]);
        });
    }
};