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
        Schema::table('actualizations', function (Blueprint $table) {
            $table -> json('route_remaining');
            $table -> foreignId('current_warehouse_id')->nullable()->constrained('warehouses');
            $table -> foreignId('next_warehouse_id')->nullable()->constrained('warehouses');
            $table -> dropColumn('last_warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actualizations', function (Blueprint $table) {
            //
        });
    }
};
