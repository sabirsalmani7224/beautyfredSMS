<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('owner_name', 150)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->decimal('gps_latitude', 10, 7)->nullable();
            $table->decimal('gps_longitude', 10, 7)->nullable();
            $table->enum('shop_type', ['retail', 'wholesale', 'distributor', 'salon', 'pharmacy', 'other'])->default('retail');
            $table->foreignId('registered_by')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index('registered_by');
            $table->index('city');
            $table->index('state');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
