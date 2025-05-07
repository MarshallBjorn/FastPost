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
            $table->unsignedBigInteger('receiver_id');
            $table->unsignedBigInteger('destination_postmat_id');

            $table->string('receiver_email');
            $table->string('receiver_phone');

            $table->enum('status', ['registered', 'in_transit', 'in_postmat', 'collected']);

            $table->dateTime('sent_at');
            $table->dateTime('delivered_at')->nullable();
            $table->dateTime('collected_at')->nullable();

            $table->timestamps();

            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('destination_postmat_id')->references('id')->on('postmats')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->nullOnDelete();
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
