<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('remissions', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 20)->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('seller_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete()->comment('Usuario que capturó');
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete()->comment('Almacén principal (puede haber por partida)');

            $table->enum('status', ['draft','open','invoiced','cancelled'])->default('draft');
            $table->date('issued_at');

            $table->decimal('subtotal', 14, 4)->default(0);
            $table->decimal('discount_total', 14, 4)->default(0);
            $table->decimal('tax_total', 14, 4)->default(0);
            $table->decimal('total', 14, 4)->default(0);
            $table->string('currency', 3)->default('MXN');

            $table->text('notes')->nullable();

            // Cancelación (solo admin)
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();

            // Facturación (fase 3)
            $table->string('invoice_id')->nullable()->comment('ID Facturama / UUID');
            $table->string('invoice_uuid', 36)->nullable();
            $table->timestamp('invoiced_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'issued_at']);
            $table->index(['customer_id']);
        });

        Schema::create('remission_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete()->comment('Almacén de origen de esta partida');
            $table->foreignId('lot_id')->nullable()->constrained('lots')->nullOnDelete();
            $table->string('description'); // snapshot
            $table->string('lot_number', 60)->nullable(); // snapshot
            $table->date('expires_at')->nullable(); // snapshot
            $table->decimal('quantity', 14, 4);
            $table->decimal('unit_price', 14, 4);
            $table->decimal('discount_rate', 5, 4)->default(0);
            $table->decimal('tax_rate', 5, 4)->default(0.16);
            $table->decimal('subtotal', 14, 4);
            $table->decimal('discount_amount', 14, 4)->default(0);
            $table->decimal('tax_amount', 14, 4);
            $table->decimal('total', 14, 4);
            $table->timestamps();

            $table->index(['remission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('remission_items');
        Schema::dropIfExists('remissions');
    }
};
