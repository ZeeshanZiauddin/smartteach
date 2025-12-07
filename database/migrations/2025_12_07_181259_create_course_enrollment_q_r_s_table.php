<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_enrollment_q_r_s', function (Blueprint $table) {
            $table->id();

            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade'); // teacher who generated QR
            $table->foreignId('course_id')->constrained()->onDelete('cascade'); // course associated

            $table->string('code')->unique(); // unique QR code string / enrollment URL identifier
            $table->string('type')->default('url'); // Larazeus QR type, default URL
            $table->string('format')->default('svg'); // output format (svg, png, etc.)
            $table->integer('size')->default(300); // QR size

            $table->string('qr_url'); // the URL or data the QR encodes
            $table->text('qr_options')->nullable(); // stores Larazeus QR options (colors, style, etc.)

            $table->integer('limit')->default(10); // max students
            $table->integer('used')->default(0); // number of students enrolled

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_enrollment_q_r_s');
    }
};