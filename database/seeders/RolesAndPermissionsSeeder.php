<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // Course permissions
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',
            'publish courses',
            // Lesson permissions
            'view lessons',
            'create lessons',
            'edit lessons',
            'delete lessons',
            // Assignment permissions
            'view assignments',
            'create assignments',
            'edit assignments',
            'delete assignments',
            'grade assignments',
            // Quiz permissions
            'view quizzes',
            'create quizzes',
            'edit quizzes',
            'delete quizzes',
            'take quizzes',
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage roles',
            // Enrollment
            'view enrollments',
            'enroll courses',
            'manage enrollments',
            // Analytics & Reports
            'view analytics',
            'export reports',
            // Settings
            'manage settings',
            'manage payments',

            // Forum
            'view forums',
            'create threads',
            'edit threads',
            'delete threads',
            'pin threads',
            'lock threads',
            'solve threads',
            'create posts',
            'edit posts',
            'delete posts',
            'like posts',
            'report posts',
            'moderate forums',

            // Event management
            'view events',
            'create events',
            'edit events',
            'delete events',
            'publish events',

            // Event registration management
            'view registrations',
            'manage registrations',
            'approve registrations',
            'cancel registrations',
            'checkin participants',

            // Event settings
            'manage event categories',
            'manage event types',
            'export event reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $instructorRole = Role::firstOrCreate(['name' => 'instructor', 'guard_name' => 'web']);
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $supportRole = Role::firstOrCreate(['name' => 'support', 'guard_name' => 'web']);
        $eventManagerRole = Role::firstOrCreate(['name' => 'event_manager', 'guard_name' => 'web']);

        // Assign permissions to roles
        $adminRole->syncPermissions(Permission::all());

        $instructorRole->syncPermissions([
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',
            'publish courses',
            'view lessons',
            'create lessons',
            'edit lessons',
            'delete lessons',
            'view assignments',
            'create assignments',
            'edit assignments',
            'delete assignments',
            'grade assignments',
            'view quizzes',
            'create quizzes',
            'edit quizzes',
            'delete quizzes',
            'view enrollments',
            'view analytics',
            'export reports',
            'view forums',
            'create threads',
            'create posts',
            'lock threads',
            'solve threads'
        ]);

        $studentRole->syncPermissions([
            'view courses',
            'take quizzes',
            'enroll courses',
            'view enrollments',
            'view forums',
            'create threads',
            'create posts',
            'like posts',
            'report posts'
        ]);

        $supportRole->syncPermissions([
            'view users',
            'view courses',
            'view enrollments',
            'view analytics',
            'report posts',
            'moderate forums',
        ]);

        $eventManagerRole->syncPermissions([
            'view users',
            'view courses',
            'view events',
            'create events',
            'edit events',
            'delete events',
            'publish events',
            'view registrations',
            'manage registrations',
            'approve registrations',
            'cancel registrations',
            'checkin participants',
            'manage event categories',
            'manage event types',
            'export event reports',
        ]);

        $this->command->info('Roles and permissions seeded successfully.');
    }
}