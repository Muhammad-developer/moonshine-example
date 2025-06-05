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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users'); // ученик
            $table->foreignId('subject_id')->constrained('subjects'); // предмет
            $table->date('date');
            $table->enum('value', ['1', '2', '3', '4', '5']);
            //$table->enum('value', ['A', 'B', 'C', 'D', 'F']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
