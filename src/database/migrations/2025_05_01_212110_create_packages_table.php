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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('pickup_code')->nullable();

            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('goal_postmat_id');

            $table->string('reciever_email');
            $table->string('reciever_phone');
            $table->unsignedBigInteger('reciever_id');

            $table->enum('status', ['registered', 'on_the_way', 'in_delivery', 'in_postmat', 'delivered']);
            
            $table->dateTime('register_date');
            $table->dateTime('delivered_date')->nullable();
            $table->dateTime('recieval_date')->nullable();
            
            $table->timestamps();
            
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('goal_postmat_id')->references('id')->on('postmats')->onDelete('cascade');
            $table->foreign('reciever_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
