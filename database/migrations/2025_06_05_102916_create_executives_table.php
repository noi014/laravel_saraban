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
        // Schema::create('executives', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });
        Schema::create('executives', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // เช่น นายก อบต., ปลัด อบต.
    $table->string('position')->nullable(); // เช่น นายกเทศมนตรี
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('executives');
    }
};
