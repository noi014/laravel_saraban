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
        // Schema::create('memos', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });
        Schema::create('memos', function (Blueprint $table) {
    $table->id();
    $table->string('memo_number');
    $table->date('memo_date');
    $table->string('subject');
    $table->foreignId('executive_id')->constrained('executives')->onDelete('cascade');
    $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
    $table->string('file_path')->nullable();
    $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
    $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memos');
    }
};
