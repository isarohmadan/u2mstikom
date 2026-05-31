<?php

if (!function_exists('formatBytes')) {
    /**
     * Format bytes to a human-readable format
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('translatePermission')) {
    /**
     * Translate permission name to Indonesian
     *
     * @param string $permissionName
     * @return string
     */
    function translatePermission($permissionName) {
        $translations = [
            // Users permissions
            'users.view' => 'Lihat Pengguna',
            'users.create' => 'Buat Pengguna',
            'users.edit' => 'Edit Pengguna',
            'users.delete' => 'Hapus Pengguna',
            'users.settings' => 'Pengaturan Pengguna',
            
            // Roles permissions
            'roles.manage' => 'Kelola Role',
            
            // Topics permissions
            'topics.view' => 'Lihat Topik',
            'topics.create' => 'Buat Topik',
            'topics.edit' => 'Edit Topik',
            'topics.delete' => 'Hapus Topik',
            'topics.approve' => 'Setujui Topik',
            'topics.bookmark' => 'Tandai Topik',
            'topics.my' => 'Topik Saya',
            
            // Answers permissions
            'answers.delete' => 'Hapus Jawaban',
            'answers.create' => 'Buat Jawaban',
            'answers.edit' => 'Edit Jawaban',
            'answers.vote' => 'Vote Jawaban',
            'answers.verify' => 'Verifikasi Jawaban',
            'answers.manage' => 'Kelola Jawaban',
            
            // Comments permissions
            'comments.delete' => 'Hapus Komentar',
            'comments.create' => 'Buat Komentar',
            'comments.edit' => 'Edit Komentar',
            'comments.manage' => 'Kelola Komentar',
            
            // Announcements permissions
            'announcements.create' => 'Buat Pengumuman',
            'announcements.edit' => 'Edit Pengumuman',
            'announcements.delete' => 'Hapus Pengumuman',
            'announcements.view' => 'Lihat Pengumuman',
            'announcements.publish' => 'Publikasi Pengumuman',
            'announcements.manage' => 'Kelola Pengumuman',
            
            // Categories permissions
            'categories.manage' => 'Kelola Kategori',
            'categories.create' => 'Buat Kategori',
            'categories.edit' => 'Edit Kategori',
            'categories.delete' => 'Hapus Kategori',
            'categories.view' => 'Lihat Kategori',
            
            // Templates permissions
            'templates.view' => 'Lihat Template',
            'templates.manage' => 'Kelola Template',
            'templates.create' => 'Buat Template',
            'templates.edit' => 'Edit Template',
            'templates.delete' => 'Hapus Template',
            'templates.download' => 'Unduh Template',
            'templates.upload' => 'Unggah Template',
            
            // Lessons permissions
            'lessons.view' => 'Lihat Pembelajaran',
            'lessons.create' => 'Buat Pembelajaran',
            'lessons.edit' => 'Edit Pembelajaran',
            'lessons.delete' => 'Hapus Pembelajaran',
            'lessons.download' => 'Unduh Pembelajaran',
            'lessons.publish' => 'Publikasi Pembelajaran',
            'lessons.statistics' => 'Statistik Pembelajaran',
            'lessons.manage' => 'Kelola Pembelajaran',
            
            // Quizzes permissions
            'quizzes.view' => 'Lihat Kuis',
            'quizzes.create' => 'Buat Kuis',
            'quizzes.edit' => 'Edit Kuis',
            'quizzes.delete' => 'Hapus Kuis',
            'quizzes.take' => 'Ikuti Kuis',
            'quizzes.publish' => 'Publikasi Kuis',
            'quizzes.results' => 'Hasil Kuis',
            'quizzes.statistics' => 'Statistik Kuis',
            'quizzes.manage' => 'Kelola Kuis',
            
            // Topics my permissions
            'topics.my.delete' => 'Hapus Topik Saya',
            'topics.my.edit' => 'Edit Topik Saya',
            
            // Peserta permissions
            'peserta.manage' => 'Kelola Peserta',
        ];
        
        // Return translation if exists, otherwise return formatted original
        if (isset($translations[$permissionName])) {
            return $translations[$permissionName];
        }
        
        // Fallback: format the permission name if translation doesn't exist
        $parts = explode('.', $permissionName);
        if (count($parts) === 2) {
            $module = ucfirst($parts[0]);
            $action = ucfirst($parts[1]);
            return $action . ' ' . $module;
        }
        
        return $permissionName;
    }
}

if (!function_exists('translatePermissionGroup')) {
    /**
     * Translate permission group name to Indonesian
     *
     * @param string $groupName
     * @return string
     */
    function translatePermissionGroup($groupName) {
        $translations = [
            'users' => 'Pengguna',
            'roles' => 'Role',
            'topics' => 'Topik',
            'answers' => 'Jawaban',
            'comments' => 'Komentar',
            'announcements' => 'Pengumuman',
            'categories' => 'Kategori',
            'templates' => 'Template',
            'lessons' => 'Pembelajaran',
            'quizzes' => 'Kuis',
            'peserta' => 'Peserta',
        ];
        
        return $translations[$groupName] ?? ucfirst($groupName);
    }
}
