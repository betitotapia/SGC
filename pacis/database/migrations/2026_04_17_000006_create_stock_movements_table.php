<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lot_id')->nullable()->constrained('lots')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('type', [
                'in',             // Entrada genérica
                'out',            // Salida genérica
                'purchase',       // Entrada por recepción de OC
                'remission',      // Salida por remisión
                'remission_cancel',// Devolución por cancelación de remisión
                'transfer_in',    // Entrada por traspaso
                'transfer_out',   // Salida por traspaso
                'adjustment',     // Ajuste manual (merma, inventario)
                'initial',        // Saldo inicial
            ]);

            $table->decimal('quantity', 14, 4)->comment('Positivo salida/entrada, signo según type');
            $table->decimal('unit_cost', 14, 4)->default(0);

            // Referencia polimórfica al documento origen
            $table->nullableMorphs('reference');
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();

            $table->timestamp('moved_at')->useCurrent();
            $table->timestamps();

            $table->index(['product_id', 'warehouse_id', 'moved_at']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
