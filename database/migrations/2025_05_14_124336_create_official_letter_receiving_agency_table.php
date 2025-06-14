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
        // Schema::create('official_letter_receiving_agency', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });

        Schema::create('official_letter_receiving_agency', function (Blueprint $table) {
            $table->id();
            $table->foreignId('official_letter_id')->constrained()->onDelete('cascade');
            $table->foreignId('receiving_agency_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_letter_receiving_agency');
    }
};
