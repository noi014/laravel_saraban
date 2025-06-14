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
        // Schema::create('announcements', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });
        Schema::create('announcements', function (Blueprint $table) {
    $table->id();
    $table->string('announcement_number')->unique();
    $table->string('announcement_name');
    $table->date('announcement_date');
    $table->string('file_path')->nullable();
    $table->unsignedBigInteger('created_by');
    $table->unsignedBigInteger('updated_by')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
