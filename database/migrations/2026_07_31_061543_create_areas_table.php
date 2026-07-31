<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete();
            $table->foreignId('asm_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('region_id');
            $table->index('asm_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
