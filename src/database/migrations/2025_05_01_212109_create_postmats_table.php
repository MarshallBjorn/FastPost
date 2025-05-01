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
        Schema::create('postmats', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string('city');
            $table->string("post-code");
            $table->decimal("latitude", 10, 7);
            $table->decimal("longtitude", 10, 7);
            $table->enum('status', ['active', 'n/a', 'service']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postmats');
    }
};
