<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quality_tasks', function (Blueprint $table) {
            $table->text('comments')->nullable()->after('description');
            $table->text('review_comment')->nullable()->after('comments');
            $table->foreignId('reviewed_by')->nullable()->after('review_comment')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('quality_tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['comments', 'review_comment', 'reviewed_at']);
        });
    }
};