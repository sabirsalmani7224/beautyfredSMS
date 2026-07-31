<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->foreignId('zone_id')->constrained('zones')->cascadeOnDelete();
            $table->foreignId('rsm_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('states_covered')->nullable(); // JSON array of state names
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('zone_id');
            $table->index('rsm_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
