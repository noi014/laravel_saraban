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
        // Schema::create('sending_letters', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });

        Schema::create('sending_letters', function (Blueprint $table) {
    $table->id();
    $table->string('doc_number');
    $table->date('doc_date');
    $table->string('subject');
    $table->foreignId('sender_id')->constrained('executives')->onDelete('cascade');
    $table->string('file_path')->nullable();
    $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
    $table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sending_letters');
    }
};
