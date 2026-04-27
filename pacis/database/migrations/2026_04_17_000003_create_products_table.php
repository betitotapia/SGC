<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 15)->unique()->comment('SAT code, ej. H87 (Pieza)');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // Identificadores
            $table->string('reference', 60)->unique()->comment('Referencia principal / SKU');
            $table->string('alt_key', 60)->nullable()->index()->comment('Clave alterna / clave proveedor');
            $table->string('barcode', 64)->nullable()->unique()->comment('EAN/UPC/Code128 — escaneable');
            $table->boolean('barcode_generated')->default(false)->comment('true si lo generó el sistema');

            // Descripción
            $table->string('description');
            $table->text('long_description')->nullable();
            $table->string('brand')->nullable();
            $table->string('presentation')->nullable()->comment('Ej. Caja 100 pzas');

            // Clasificación
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('product_units')->nullOnDelete();

            // Características para insumos médicos
            $table->boolean('requires_lot')->default(true);
            $table->boolean('requires_expiry')->default(true);
            $table->boolean('controlled')->default(false)->comment('Medicamento controlado / grupo SSA');
            $table->string('sat_product_code', 10)->nullable()->comment('c_ClaveProdServ SAT');

            // Precios (se usarán en remisión/facturación)
            $table->decimal('cost', 14, 4)->default(0);
            $table->decimal('price', 14, 4)->default(0);
            $table->decimal('tax_rate', 5, 4)->default(0.16)->comment('IVA por defecto 16%');

            // Control
            $table->integer('min_stock')->default(0);
            $table->integer('max_stock')->nullable();
            $table->boolean('active')->default(true);
            $table->string('image_path')->nullable();

            $table->timestamps();

            $table->index(['active']);
            $table->index(['description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_units');
        Schema::dropIfExists('product_categories');
    }
};
