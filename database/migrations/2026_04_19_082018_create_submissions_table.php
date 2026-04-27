<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->unsignedBigInteger('student_id')->comment('ID user yang mengirim tugas');
            $table->text('content')->nullable();
            $table->json('attachments')->nullable();
            $table->integer('submission_count')->default(1);
            $table->boolean('is_late')->default(false);
            $table->enum('status', ['draft', 'submitted', 'graded', 'returned', 'late'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->text('feedback')->nullable();
            $table->integer('score')->nullable();
            $table->float('ai_score')->nullable();
            $table->float('plagiarism_percentage')->nullable();
            $table->unsignedBigInteger('graded_by')->nullable();
            $table->json('rubric_scores')->nullable(); // Simpan skor per kriteria
            $table->timestamps();

            $table->foreign('assignment_id')->references('id')->on('assignments')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('graded_by')->references('id')->on('users')->onDelete('set null');
            $table->unique(['assignment_id', 'student_id']);
            $table->index(['student_id', 'status']);
            $table->index(['assignment_id', 'status']);
            $table->index('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};