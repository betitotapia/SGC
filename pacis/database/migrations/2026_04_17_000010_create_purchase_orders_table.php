<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 20)->unique();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete()->comment('Almacén destino por defecto');
            $table->foreignId('user_id')->constrained()->restrictOnDelete()->comment('Usuario que creó la OC');

            $table->enum('status', ['draft','sent','partial','received','cancelled'])->default('draft');
            $table->date('ordered_at');
            $table->date('expected_at')->nullable();
            $table->date('closed_at')->nullable();

            $table->decimal('subtotal', 14, 4)->default(0);
            $table->decimal('tax_total', 14, 4)->default(0);
            $table->decimal('total', 14, 4)->default(0);
            $table->string('currency', 3)->default('MXN');
            $table->decimal('exchange_rate', 12, 6)->default(1);

            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'ordered_at']);
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('description'); // snapshot
            $table->decimal('quantity_ordered', 14, 4);
            $table->decimal('quantity_received', 14, 4)->default(0);
            $table->decimal('unit_cost', 14, 4);
            $table->decimal('tax_rate', 5, 4)->default(0.16);
            $table->decimal('subtotal', 14, 4);
            $table->decimal('tax_amount', 14, 4);
            $table->decimal('total', 14, 4);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['purchase_order_id']);
        });

        Schema::create('receptions', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 20)->unique();
            $table->foreignId('purchase_order_id')->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->date('received_at');
            $table->string('supplier_invoice', 40)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('reception_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reception_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('lot_id')->nullable()->constrained('lots')->nullOnDelete();
            $table->string('lot_number', 60)->nullable();
            $table->date('expires_at')->nullable();
            $table->decimal('quantity', 14, 4);
            $table->decimal('unit_cost', 14, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reception_items');
        Schema::dropIfExists('receptions');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
