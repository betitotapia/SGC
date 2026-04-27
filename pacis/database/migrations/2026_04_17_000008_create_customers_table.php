<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->foreignId('fiscal_profile_id')->nullable()->unique()->constrained('fiscal_profiles')->nullOnDelete();
            $table->string('display_name')->comment('Nombre comercial/corto para mostrar');
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->decimal('credit_limit', 14, 2)->default(0);
            $table->integer('credit_days')->default(0);
            $table->string('price_list')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('seller_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['active', 'display_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
