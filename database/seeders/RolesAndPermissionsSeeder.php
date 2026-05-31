<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create all permissions
        $permissions = [
            // User Management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.settings',

            // Role Management
            'roles.manage',

            // Topics (Forum) - General
            'topics.view',
            'topics.create',
            'topics.edit',      // Can edit ALL topics (admin/staff only)
            'topics.delete',    // Can delete ALL topics (admin/staff only)
            'topics.approve',
            'topics.bookmark',

            // Topics - My Topics (for own topics)
            'topics.my',        // Can view own topics page
            'topics.my.edit',   // Can edit OWN topics only
            'topics.my.delete', // Can delete OWN topics only

            // Answers & Comments
            'answers.create',
            'answers.edit',        // Can edit own answers
            'answers.delete',     // Can delete own answers
            'answers.vote',
            'answers.verify',
            'answers.manage',     // Can manage all answers (admin/pengurus)
            'comments.create',
            'comments.edit',      // Can edit own comments
            'comments.delete',    // Can delete own comments
            'comments.manage',    // Can manage all comments (admin/pengurus)

            // Announcements
            'announcements.view',
            'announcements.create',
            'announcements.edit',
            'announcements.delete',
            'announcements.publish',
            'announcements.manage', // Full manage (backward compatibility)

            // Categories
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            'categories.manage', // Full manage (backward compatibility)

            // Templates (KMS)
            'templates.view',
            'templates.create',
            'templates.edit',
            'templates.delete',
            'templates.download',
            'templates.upload',
            'templates.manage', // Full manage (backward compatibility)

            // Peserta
            'peserta.manage',

            // Lessons (E-Learning)
            'lessons.view',
            'lessons.create',
            'lessons.edit',
            'lessons.delete',
            'lessons.publish',    // Can publish/unpublish lessons
            'lessons.download',   // Can download lesson materials
            'lessons.statistics', // Can view lesson statistics
            'lessons.manage',     // Full manage (backward compatibility)

            // Quizzes
            'quizzes.view',
            'quizzes.create',
            'quizzes.edit',
            'quizzes.delete',
            'quizzes.publish',    // Can publish/unpublish quizzes
            'quizzes.take',       // Can take quizzes
            'quizzes.results',    // Can view quiz results
            'quizzes.statistics', // Can view quiz statistics
            'quizzes.manage',    // Full manage (backward compatibility)
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ========================================
        // ROLES WITH PERMISSIONS
        // ========================================

        // ADMINISTRATOR - Full access
        $administrator = Role::firstOrCreate(['name' => 'administrator']);
        $administrator->syncPermissions(Permission::all());

        // PENGURUS - Most permissions (can edit/delete ALL topics)
        $pengurus = Role::firstOrCreate(['name' => 'pengurus']);
        $pengurus->syncPermissions([
            'users.view',
            'users.settings',
            'topics.view',
            'topics.create',
            'topics.edit',
            'topics.delete',
            'topics.approve',
            'topics.bookmark',
            'topics.my',
            'topics.my.edit',
            'topics.my.delete',
            'answers.create',
            'answers.edit',
            'answers.delete',
            'answers.vote',
            'answers.verify',
            'answers.manage',
            'comments.create',
            'comments.edit',
            'comments.delete',
            'comments.manage',
            'announcements.view',
            'announcements.create',
            'announcements.edit',
            'announcements.delete',
            'announcements.publish',
            'announcements.manage',
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            'categories.manage',
            'templates.view',
            'templates.create',
            'templates.edit',
            'templates.delete',
            'templates.download',
            'templates.upload',
            'templates.manage',
            'peserta.manage',
            'lessons.view',
            'lessons.create',
            'lessons.edit',
            'lessons.delete',
            'lessons.publish',
            'lessons.download',
            'lessons.statistics',
            'lessons.manage',
            'quizzes.view',
            'quizzes.create',
            'quizzes.edit',
            'quizzes.delete',
            'quizzes.publish',
            'quizzes.take',
            'quizzes.results',
            'quizzes.statistics',
            'quizzes.manage',
        ]);

        // ANGGOTA - Basic member access (can only edit/delete OWN topics)
        $anggota = Role::firstOrCreate(['name' => 'anggota']);
        $anggota->syncPermissions([
            'topics.view',
            'topics.create',
            'topics.bookmark',
            'topics.my',
            'topics.my.edit',
            'topics.my.delete',  // Can manage OWN topics
            'answers.create',
            'answers.edit',      // Can edit own answers
            'answers.delete',    // Can delete own answers
            'answers.vote',
            'comments.create',
            'comments.edit',      // Can edit own comments
            'comments.delete',   // Can delete own comments
            'announcements.view', // Can view announcements
            'templates.view',
            'templates.download', // Can download templates
            'categories.view',    // Can view categories
            'lessons.view',       // Can view lessons
            'lessons.download',   // Can download lesson materials
            'quizzes.view',       // Can view quizzes
            'quizzes.take',       // Can take quizzes
            'quizzes.results',    // Can view own quiz results
        ]);
    }
}
