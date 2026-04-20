<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // 1. CREATE ADMIN USER
        // ============================================
        $admin = User::firstOrCreate(
            ['email' => 'admin@lms.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'default_role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        Profile::updateOrCreate(
            ['profileable_id' => $admin->id, 'profileable_type' => User::class],
            [
                'profile_type' => 'admin',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'nickname' => 'Admin',
                // 'email' => 'admin@lms.com',  // <-- HAPUS BARIS INI
                'phone' => '081234567890',
                'is_active' => true,
                'approval_status' => 'approved',
                'approved_at' => now(),
                'personal_info' => json_encode([
                    'bio' => 'System Administrator',
                    'position' => 'Super Administrator',
                ]),
            ]
        );

        // ============================================
        // 2. CREATE INSTRUCTOR USER
        // ============================================
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@lms.com'],
            [
                'name' => 'John Instructor',
                'password' => Hash::make('password'),
                'default_role' => 'instructor',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $instructor->assignRole('instructor');

        Profile::updateOrCreate(
            ['profileable_id' => $instructor->id, 'profileable_type' => User::class],
            [
                'profile_type' => 'instructor',
                'first_name' => 'John',
                'last_name' => 'Instructor',
                'nickname' => 'John',
                // 'email' => 'instructor@lms.com',  // <-- HAPUS
                'phone' => '081234567891',
                'is_active' => true,
                'approval_status' => 'approved',
                'approved_at' => now(),
                'professional_info' => json_encode([
                    'job_title' => 'Senior Web Developer',
                    'company' => 'Tech Academy',
                    'years_of_experience' => 10,
                    'expertise' => ['Laravel', 'Vue.js', 'React', 'Tailwind CSS'],
                    'qualifications' => ['Master of Computer Science', 'Certified Laravel Developer'],
                    'languages' => ['Indonesian', 'English'],
                ]),
                'social_media' => json_encode([
                    'linkedin' => 'https://linkedin.com/in/johninstructor',
                    'github' => 'https://github.com/johninstructor',
                ]),
            ]
        );

        // ============================================
        // 3. CREATE STUDENT USER
        // ============================================
        $student = User::firstOrCreate(
            ['email' => 'student@lms.com'],
            [
                'name' => 'Jane Student',
                'password' => Hash::make('password'),
                'default_role' => 'student',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $student->assignRole('student');

        Profile::updateOrCreate(
            ['profileable_id' => $student->id, 'profileable_type' => User::class],
            [
                'profile_type' => 'student',
                'first_name' => 'Jane',
                'last_name' => 'Student',
                'nickname' => 'Jane',
                // 'email' => 'student@lms.com',  // <-- HAPUS
                'phone' => '081234567892',
                'whatsapp' => '081234567892',
                'is_active' => true,
                'approval_status' => 'approved',
                'approved_at' => now(),
                'academic_info' => json_encode([
                    'student_id' => 'STU2024001',
                    'school_name' => 'Tech University',
                    'grade_level' => 'Senior Year',
                    'major' => 'Computer Science',
                    'enrollment_year' => 2024,
                ]),
                'preferences' => json_encode([
                    'language' => 'en',
                    'timezone' => 'Asia/Jakarta',
                    'notification_settings' => [
                        'email' => true,
                        'push' => true,
                    ],
                ]),
                'statistics' => json_encode([
                    'total_courses_enrolled' => 0,
                    'total_courses_completed' => 0,
                    'total_certificates' => 0,
                    'total_points' => 0,
                    'current_level' => 1,
                ]),
            ]
        );

        // ============================================
        // 4. CREATE SUPPORT USER (Optional)
        // ============================================
        $support = User::firstOrCreate(
            ['email' => 'support@lms.com'],
            [
                'name' => 'Support Agent',
                'password' => Hash::make('password'),
                'default_role' => 'support',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $support->assignRole('support');

        Profile::updateOrCreate(
            ['profileable_id' => $support->id, 'profileable_type' => User::class],
            [
                'profile_type' => 'support',
                'first_name' => 'Support',
                'last_name' => 'Agent',
                'nickname' => 'Support',
                // 'email' => 'support@lms.com',  // <-- HAPUS
                'phone' => '081234567893',
                'is_active' => true,
                'approval_status' => 'approved',
                'approved_at' => now(),
            ]
        );

        // ============================================
        // 5. SAMPLE USERS (Only in local)
        // ============================================
        if (app()->environment('local')) {
            // 10 additional students
            for ($i = 1; $i <= 10; $i++) {
                $testStudent = User::firstOrCreate(
                    ['email' => "student{$i}@example.com"],
                    [
                        'name' => "Test Student {$i}",
                        'password' => Hash::make('password'),
                        'default_role' => 'student',
                        'is_active' => true,
                        'email_verified_at' => now(),
                    ]
                );
                $testStudent->assignRole('student');
                
                Profile::updateOrCreate(
                    ['profileable_id' => $testStudent->id, 'profileable_type' => User::class],
                    [
                        'profile_type' => 'student',
                        'first_name' => "Test",
                        'last_name' => "Student {$i}",
                        'nickname' => "Student{$i}",
                        // 'email' => "student{$i}@example.com",  // <-- HAPUS
                        'is_active' => true,
                        'approval_status' => 'approved',
                        'approved_at' => now(),
                        'academic_info' => json_encode([
                            'student_id' => "STU2024" . str_pad($i, 3, '0', STR_PAD_LEFT),
                            'school_name' => 'Test University',
                        ]),
                    ]
                );
            }
            
            // 3 additional instructors
            for ($i = 1; $i <= 3; $i++) {
                $testInstructor = User::firstOrCreate(
                    ['email' => "instructor{$i}@example.com"],
                    [
                        'name' => "Test Instructor {$i}",
                        'password' => Hash::make('password'),
                        'default_role' => 'instructor',
                        'is_active' => true,
                        'email_verified_at' => now(),
                    ]
                );
                $testInstructor->assignRole('instructor');
                
                Profile::updateOrCreate(
                    ['profileable_id' => $testInstructor->id, 'profileable_type' => User::class],
                    [
                        'profile_type' => 'instructor',
                        'first_name' => "Test",
                        'last_name' => "Instructor {$i}",
                        'nickname' => "Instructor{$i}",
                        // 'email' => "instructor{$i}@example.com",  // <-- HAPUS
                        'is_active' => true,
                        'approval_status' => 'approved',
                        'approved_at' => now(),
                        'professional_info' => json_encode([
                            'job_title' => 'Instructor',
                            'years_of_experience' => 5,
                            'expertise' => ['Web Development', 'Programming'],
                        ]),
                    ]
                );
            }
        }

        $this->command->info('Users seeded successfully!');
        $this->command->warn('Default users created:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@lms.com', 'password'],
                ['Instructor', 'instructor@lms.com', 'password'],
                ['Student', 'student@lms.com', 'password'],
                ['Support', 'support@lms.com', 'password'],
            ]
        );
    }
}