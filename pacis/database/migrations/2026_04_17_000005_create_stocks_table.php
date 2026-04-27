<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lot_id')->nullable()->constrained('lots')->nullOnDelete();

            $table->decimal('quantity', 14, 4)->default(0);
            $table->decimal('reserved', 14, 4)->default(0)->comment('Reservado por remisiones pendientes');
            $table->decimal('avg_cost', 14, 4)->default(0)->comment('Costo promedio ponderado');

            $table->timestamps();

            // Un stock row único por combinación producto+almacén+lote
            $table->unique(['product_id', 'warehouse_id', 'lot_id'], 'stocks_pwl_unique');
            $table->index(['warehouse_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
