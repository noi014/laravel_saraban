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
            $table->string('doc_number'); // เลขที่หนังสือลงรับ
            $table->date('doc_date'); // วันที่หนังสือลงรับ
            $table->string('from_agency'); // หนังสือจากหน่วยงาน
            $table->string('to_agency'); // หนังสือส่งถึง
            $table->string('subject'); // ชื่อเรื่อง
            $table->string('receiver_department'); // หน่วยงานที่รับ
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
