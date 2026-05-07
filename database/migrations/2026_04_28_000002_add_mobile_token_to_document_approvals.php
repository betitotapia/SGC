<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('document_approvals', function (Blueprint $table) {
            $table->string('mobile_token', 80)->nullable()->unique()->after('signature_image');
            $table->timestamp('mobile_token_expires_at')->nullable()->after('mobile_token');
        });
    }

    public function down(): void
    {
        Schema::table('document_approvals', function (Blueprint $table) {
            $table->dropColumn(['mobile_token', 'mobile_token_expires_at']);
        });
    }
};
