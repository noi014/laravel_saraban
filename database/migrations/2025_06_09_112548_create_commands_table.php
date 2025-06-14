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
       Schema::create('commands', function (Blueprint $table) {
    $table->id();
    $table->string('command_number')->unique();
    $table->string('command_name');
    $table->date('command_date');
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
        Schema::dropIfExists('commands');
    }
};
