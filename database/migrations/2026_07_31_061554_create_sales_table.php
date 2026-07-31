<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // PSM or BA
            $table->foreignId('shop_id')->nullable()->constrained('shops')->nullOnDelete();
            $table->string('event_name', 200)->nullable(); // For BA event/stall sales
            $table->enum('sale_type', ['shop_sale', 'event_sale'])->default('shop_sale');
            $table->date('sale_date');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->enum('payment_mode', ['cash', 'upi', 'credit', 'cheque', 'mixed'])->default('cash');
            $table->string('payment_reference', 200)->nullable();
            $table->decimal('gps_latitude', 10, 7)->nullable();
            $table->decimal('gps_longitude', 10, 7)->nullable();
            $table->string('photo_proof')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('user_id');
            $table->index('sale_date');
            $table->index('status');
            $table->index('sale_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
