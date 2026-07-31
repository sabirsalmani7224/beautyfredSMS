<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// This migration is a placeholder — users table is fully defined in 0001_01_01_000000_create_users_table.php
// Adding asm_id for BA State Supervisors matrix relationship
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // BA State Supervisors also report to ASMs (matrix relationship)
            $table->foreignId('asm_id')->nullable()->after('parent_id')
                ->constrained('users')->nullOnDelete()
                ->comment('For BA State Supervisors: which ASM they also report to');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('asm_id');
        });
    }
};
