<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('postmats', function (Blueprint $table) {
            $table->foreignId('warehouse_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete(); // optional, handles foreign key cleanup
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postmats', function (Blueprint $table) {
            //
        });
    }
};
