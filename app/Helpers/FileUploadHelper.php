<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Helper class untuk upload file yang kompatibel dengan shared hosting
 * 
 * Strategy:
 * 1. Upload ke Laravel storage (storage/app/public/) - ini standard Laravel
 * 2. Copy file ke public_html/storage/ - agar accessible via browser
 * 
 * Requires .env:
 * PUBLIC_HTML_PATH=/home/username/public_html
 */
class FileUploadHelper
{
    /**
     * Upload file dan copy ke public_html
     * 
     * @param UploadedFile $file File yang akan diupload
     * @param string $directory Direktori tujuan (e.g., 'lessons', 'topics/1/attachments')
     * @param string|null $filename Nama file custom (optional)
     * @return string Path relatif file yang tersimpan
     */
    public static function upload(UploadedFile $file, string $directory, ?string $filename = null): string
    {
        // 1. Upload ke Laravel storage dulu (standard)
        if ($filename) {
            $path = $file->storeAs($directory, $filename, 'public');
        } else {
            $path = $file->store($directory, 'public');
        }

        // 2. Copy ke public_html jika dikonfigurasi
        self::copyToPublicHtml($path);

        return $path;
    }

    /**
     * Copy file dari storage ke public_html/storage
     * 
     * @param string $path Path relatif file
     * @return bool
     */
    public static function copyToPublicHtml(string $path): bool
    {
        $publicHtmlPath = env('PUBLIC_HTML_PATH');
        
        // Skip jika tidak dikonfigurasi (development mode)
        if (empty($publicHtmlPath)) {
            return false;
        }

        $source = storage_path('app/public/' . $path);
        $destination = $publicHtmlPath . '/storage/' . $path;

        // Buat direktori jika belum ada
        if (!is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }

        // Copy file
        if (file_exists($source)) {
            return copy($source, $destination);
        }

        return false;
    }

    /**
     * Hapus file dari storage DAN public_html
     * 
     * @param string $path Path relatif file
     * @return bool
     */
    public static function delete(string $path): bool
    {
        // Hapus dari Laravel storage
        $deleted = Storage::disk('public')->delete($path);

        // Hapus dari public_html juga
        $publicHtmlPath = env('PUBLIC_HTML_PATH');
        if (!empty($publicHtmlPath)) {
            $publicFile = $publicHtmlPath . '/storage/' . $path;
            if (file_exists($publicFile)) {
                @unlink($publicFile);
            }
        }

        return $deleted;
    }

    /**
     * Cek apakah file ada
     * 
     * @param string $path Path relatif file
     * @return bool
     */
    public static function exists(string $path): bool
    {
        return Storage::disk('public')->exists($path);
    }

    /**
     * Dapatkan URL public dari file
     * 
     * @param string $path Path relatif file
     * @return string
     */
    public static function url(string $path): string
    {
        return asset('storage/' . ltrim($path, '/'));
    }

    /**
     * Dapatkan path absolut dari file (di Laravel storage)
     * 
     * @param string $path Path relatif file
     * @return string
     */
    public static function path(string $path): string
    {
        return Storage::disk('public')->path($path);
    }

    /**
     * Pindahkan file dari satu lokasi ke lokasi lain
     * 
     * @param string $from Path asal
     * @param string $to Path tujuan
     * @return bool
     */
    public static function move(string $from, string $to): bool
    {
        $moved = Storage::disk('public')->move($from, $to);

        // Jika berhasil, update juga di public_html
        if ($moved) {
            $publicHtmlPath = env('PUBLIC_HTML_PATH');
            if (!empty($publicHtmlPath)) {
                $sourcePublic = $publicHtmlPath . '/storage/' . $from;
                $destPublic = $publicHtmlPath . '/storage/' . $to;

                // Buat direktori tujuan
                if (!is_dir(dirname($destPublic))) {
                    mkdir(dirname($destPublic), 0755, true);
                }

                // Pindahkan file
                if (file_exists($sourcePublic)) {
                    rename($sourcePublic, $destPublic);
                } else {
                    // Jika file source tidak ada di public, copy dari storage
                    self::copyToPublicHtml($to);
                }
            }
        }

        return $moved;
    }

    /**
     * Buat direktori jika belum ada
     * 
     * @param string $directory Path direktori
     * @return bool
     */
    public static function makeDirectory(string $directory): bool
    {
        $created = true;

        if (!Storage::disk('public')->exists($directory)) {
            $created = Storage::disk('public')->makeDirectory($directory);
        }

        // Buat juga di public_html
        $publicHtmlPath = env('PUBLIC_HTML_PATH');
        if (!empty($publicHtmlPath)) {
            $publicDir = $publicHtmlPath . '/storage/' . $directory;
            if (!is_dir($publicDir)) {
                mkdir($publicDir, 0755, true);
            }
        }

        return $created;
    }

    /**
     * Sync semua file dari storage ke public_html
     * Berguna untuk migrasi file lama
     * 
     * @param string|null $directory Direktori spesifik atau null untuk semua
     * @return int Jumlah file yang di-copy
     */
    public static function syncToPublicHtml(?string $directory = null): int
    {
        $publicHtmlPath = env('PUBLIC_HTML_PATH');
        if (empty($publicHtmlPath)) {
            return 0;
        }

        $files = Storage::disk('public')->allFiles($directory ?? '');
        $count = 0;

        foreach ($files as $file) {
            if (self::copyToPublicHtml($file)) {
                $count++;
            }
        }

        return $count;
    }
}
