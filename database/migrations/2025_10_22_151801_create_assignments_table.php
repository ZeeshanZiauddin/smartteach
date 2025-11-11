<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')
                ->constrained()
                ->onDelete('cascade'); // deletes assignments if course deleted
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade'); // deletes assignments if user deleted
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('file')->nullable(); // path to uploaded PDF
            $table->date('due_date')->nullable();
            $table->text('key_points')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};