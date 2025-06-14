<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', ['admin', 'user'])->default('user');
        $table->boolean('approved')->default(false);
        $table->unsignedBigInteger('receiving_agency_id')->nullable();

        $table->foreign('receiving_agency_id')->references('id')->on('receiving_agencies')->nullOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
