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
        Schema::table('stashes', function (Blueprint $table) {
            // Add reservation_until column
            $table->timestamp('reserved_until')
                ->nullable()
                ->after('package_id')
                ->comment('When the reservation expires (24h from creation)');

            $table->boolean('is_package_in')
                ->default(false)
                ->after('reserved_until')
                ->comment('Whether the package has been physically placed in the stash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stashes', function (Blueprint $table) {
            //
        });
    }
};
