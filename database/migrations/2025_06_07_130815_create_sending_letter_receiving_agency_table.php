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
        // Schema::create('sending_letter_receiving_agency', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });

        Schema::create('sending_letter_receiving_agency', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sending_letter_id')->constrained('sending_letters')->onDelete('cascade');
    $table->foreignId('receiving_agency_id')->constrained()->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sending_letter_receiving_agency');
    }
};
