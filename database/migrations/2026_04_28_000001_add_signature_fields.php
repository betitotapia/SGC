<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_approvals', function (Blueprint $table) {
            $table->string('signature_image')->nullable()->after('ip_address');
        });

        Schema::table('document_versions', function (Blueprint $table) {
            $table->string('certificate_path')->nullable()->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('document_approvals', function (Blueprint $table) {
            $table->dropColumn('signature_image');
        });

        Schema::table('document_versions', function (Blueprint $table) {
            $table->dropColumn('certificate_path');
        });
    }
};
