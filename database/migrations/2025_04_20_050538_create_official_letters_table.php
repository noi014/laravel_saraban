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
        // Schema::create('official_letters', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });
        Schema::create('official_letters', function (Blueprint $table) {
    $table->id();
    $table->string('reg_number'); // เลขที่ลงรับ
    $table->date('reg_date'); // วันที่ลงรับ
    $table->string('doc_number')->nullable(); // เลขที่หนังสือลงรับ
    $table->date('doc_date')->nullable(); // วันที่หนังสือลงรับ
    $table->string('from_agency'); // หนังสือจากหน่วยงาน
    $table->json('attachments')->nullable(); // ไฟล์แนบหลายไฟล์
    $table->string('to_agency'); // หนังสือส่งถึง
    $table->string('subject'); // ชื่อเรื่อง
    $table->string('receiver_agency'); // หน่วยงานที่รับหนังสือ
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_letters');
    }
};
