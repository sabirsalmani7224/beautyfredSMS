<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_allocation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_allocation_id')->constrained('stock_allocations')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('quantity_sent');
            $table->unsignedInteger('quantity_received')->default(0);
            $table->unsignedInteger('quantity_damaged')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['stock_allocation_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_allocation_items');
    }
};
