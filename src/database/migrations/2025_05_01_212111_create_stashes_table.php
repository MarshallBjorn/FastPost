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
        Schema::create('stashes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('postmat_id');
            $table->enum("size", ["S", "M", "L"]);
            $table->unsignedBigInteger('package_id')->nullable();
            
            $table->timestamps();

            $table->foreign('postmat_id')->references('id')->on('postmats')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('packages')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stashes');
    }
};
