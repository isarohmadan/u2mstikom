# KMS Templates Module

Modul manajemen template dokumen (Pitch Deck, Laporan Keuangan) dengan versioning, RBAC, upload/download, dan log.

Fitur:

-   CRUD Template (Admin/Staff)
-   Upload versi baru (Admin/Staff)
-   Download (semua role)
-   Log download per versi
-   Tampilkan versi terbaru di UI, versi lama tetap tersedia

Teknologi:

-   Laravel 10, MySQL
-   spatie/laravel-permission untuk roles
-   Storage file: `storage/app/templates/{template_id}/...`

Akses:

-   Admin & Staff: buat template, upload versi
-   User: hanya download

Instalasi singkat:

1. Jalankan migrasi: `php artisan migrate`
2. Seed contoh: `php artisan db:seed --class=TemplateSeeder`
3. Buka UI: `/templates`

API:

-   Lihat `docs/templates-openapi.yaml`

Struktur kode utama:

-   Models: `DocumentTemplate`, `DocumentTemplateVersion`, `DocumentTemplateLog`
-   Service: `app/Services/TemplateService.php`
-   Policy: `app/Policies/DocumentTemplatePolicy.php`
-   Web Controller: `app/Http/Controllers/TemplatesController.php`
-   API Controller: `app/Http/Controllers/Api/TemplateController.php`
-   Blade: `resources/views/templates/index.blade.php`

Alur inti:

-   Admin/Staff upload template pertama → versi 1 dibuat
-   Admin/Staff upload versi baru → nomor versi bertambah, latest_version diperbarui
-   User download → log tersimpan dengan waktu dan IP

Validasi file: hanya pdf, docx, pptx, xlsx.
