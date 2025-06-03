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
        Schema::create('actualizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id');
            $table->enum('message', ['sent', 'collected', 'in_warehouse', 'in_delivery']);
            $table->unsignedBigInteger('last_courier_id')->nullable();
            $table->unsignedBigInteger('last_warehouse_id')->nullable();
            $table->dateTime('created_at');
        
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->foreign('last_courier_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('last_warehouse_id')->references('id')->on('warehouses')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actualizations');
    }
};
