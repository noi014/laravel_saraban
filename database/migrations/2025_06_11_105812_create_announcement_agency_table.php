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
        // Schema::create('announcement_agency', function (Blueprint $table) {
        //     $table->id(); 
        //     $table->timestamps();
        // });
        Schema::create('announcement_agency', function (Blueprint $table) {
    $table->id();
    $table->foreignId('announcement_id')->constrained()->onDelete('cascade');
    $table->foreignId('agency_id')->constrained('receiving_agencies')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_agency');
    }
};
