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
            $table->enum('message', ['sent', 'in_warehouse', 'in_delivery']);
            $table->unsignedBigInteger('last_courier_id')->nullable();
            $table->dateTime('created_at');
        
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->foreign('last_courier_id')->references('id')->on('users')->nullOnDelete();
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
