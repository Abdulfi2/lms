<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();

            // Polymorphic relation
            $table->morphs('profileable');
            $table->enum('profile_type', ['student', 'instructor', 'admin', 'employee', 'alumni', 'support'])->default('student');

            // Data dasar yang sering di-query
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('nickname')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('birth_date')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();

            // Identitas resmi
            $table->string('nik', 16)->unique()->nullable();
            $table->string('employee_id')->unique()->nullable();
            $table->string('student_id')->unique()->nullable();

            // Alamat regional
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('postal_code')->nullable();

            // JSON fields - Data yang jarang diakses (flexible)
            // Tambahkan kolom yang dibutuhkan oleh seeder
            $table->json('personal_info')->nullable();
            $table->json('contact_info')->nullable();
            $table->json('professional_info')->nullable();
            $table->json('academic_info')->nullable();

            // Data lainnya (tetap dipertahankan)
            $table->json('identity_documents')->nullable();
            $table->json('addresses')->nullable();
            $table->json('employment_info')->nullable();
            $table->json('bank_accounts')->nullable();
            $table->json('body_measurements')->nullable();
            $table->json('social_media')->nullable();
            $table->json('emergency_contacts')->nullable();
            $table->json('medical_info')->nullable();
            $table->json('documents')->nullable();
            $table->json('education')->nullable();
            $table->json('skills')->nullable();
            $table->json('preferences')->nullable();
            $table->json('statistics')->nullable();
            $table->json('verification')->nullable();
            $table->json('metadata')->nullable();

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(false);

            // Approval workflow
            $table->enum('approval_status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('profile_type');
            $table->index('first_name');
            $table->index('last_name');
            $table->index('nik');
            $table->index('employee_id');
            $table->index('student_id');
            $table->index('phone');
            $table->index('birth_date');
            $table->index('gender');
            $table->index('province');
            $table->index('city');
            $table->index('approval_status');
            $table->index('is_active');
            $table->index('created_at');

            // Composite indexes
            $table->index(['profile_type', 'approval_status']);
            $table->index(['province', 'city']);
            $table->index(['profile_type', 'is_active']);
            $table->index(['profileable_type', 'profile_type']);

            // Foreign key
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};