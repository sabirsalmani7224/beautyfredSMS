<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_category_id')->constrained('sub_categories')->cascadeOnDelete();
            $table->string('name', 200);
            $table->string('slug', 200)->unique();
            $table->string('sku', 100)->unique();
            $table->decimal('mrp', 10, 2)->default(0);
            $table->decimal('unit_price', 10, 2)->default(0); // selling price to shops/retailers
            $table->decimal('distributor_price', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('hsn_code', 50)->nullable();
            $table->string('weight', 50)->nullable(); // e.g. "100ml", "50g"
            $table->unsignedInteger('stock_unit')->default(1); // units per pack
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index('sub_category_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
