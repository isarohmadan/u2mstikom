<?php

namespace App\Services;

use App\Models\DocumentTemplate;
use App\Models\DocumentTemplateLog;
use App\Models\DocumentTemplateVersion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class TemplateService
{
    public function createTemplate(string $name, ?string $description, UploadedFile $file, int $userId): DocumentTemplate
    {
        $this->assertValidExtension($file);
    
        try {
            return DB::transaction(function () use ($name, $description, $file, $userId) {
                $slugBase = Str::slug($name);
                $slug = $this->uniqueSlug($slugBase);
    
                $template = DocumentTemplate::create([
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $description,
                    'latest_version_number' => 0,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
    
                $version = $this->storeNewVersion($template, $file, $userId);
                $template->update([
                    'latest_version_id' => $version->id,
                    'latest_version_number' => $version->version_number,
                ]);
    
                return $template->fresh(['latestVersion']);
            });
        } catch (Throwable $e) {
            Log::error('Failed to create document template', [
                'name' => $name,
                'user_id' => $userId,
                'message' => $e->getMessage(),
            ]);
            abort(500, 'Gagal membuat template. Silakan coba lagi.');
        }
    }

    public function storeNewVersion(DocumentTemplate $template, UploadedFile $file, int $userId): DocumentTemplateVersion
    {
        $this->assertValidExtension($file);

        try {
            $nextVersion = ($template->latest_version_number ?? 0) + 1;
            $dir = storage_path('app/templates/' . $template->id);
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
                    throw new \RuntimeException("Gagal membuat direktori untuk template file.");
                }
            }

            $filename = 'v' . $nextVersion . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $destination = $dir . '/' . $filename;

            if (!$file->move($dir, $filename)) {
                throw new \RuntimeException("Gagal memindahkan file yang diupload.");
            }

            if (!file_exists($destination)) {
                throw new \RuntimeException("File yang diupload tidak ditemukan setelah dipindahkan.");
            }

            $relativePath = 'templates/' . $template->id . '/' . $filename;

            $version = DocumentTemplateVersion::create([
                'template_id' => $template->id,
                'version_number' => $nextVersion,
                'file_path' => $relativePath,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'file_size' => filesize($destination),
                'uploaded_by' => $userId,
            ]);

            $template->update([
                'latest_version_id' => $version->id,
                'latest_version_number' => $nextVersion,
                'updated_by' => $userId,
            ]);

            return $version;
        } catch (\Throwable $e) {
            \Log::error('Gagal upload versi baru template', [
                'template_id' => $template->id ?? null,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            abort(500, 'Gagal upload versi baru. Silakan coba lagi atau hubungi admin.');
        }
    }

    public function logDownload(DocumentTemplate $template, DocumentTemplateVersion $version, int $userId, ?string $ip): DocumentTemplateLog
    {
        return DocumentTemplateLog::create([
            'template_id' => $template->id,
            'version_id' => $version->id,
            'user_id' => $userId,
            'downloaded_at' => now(),
            'ip_address' => $ip,
        ]);
    }

    public function getDownloadAbsolutePath(DocumentTemplateVersion $version): string
    {
        return storage_path('app/' . ltrim($version->file_path, '/'));
    }

    public function delete(DocumentTemplate $template): void
    {
        // Delete all versions and their files
        foreach ($template->versions as $version) {
            $absolutePath = $this->getDownloadAbsolutePath($version);
            if (file_exists($absolutePath)) {
                @unlink($absolutePath);
            }
            $version->delete();
        }

        // Optionally delete the containing directory if empty
        $dir = storage_path('app/templates/' . $template->id);
        if (is_dir($dir)) {
            @rmdir($dir);
        }

        // Delete the template itself
        $template->delete();
    }

    protected function assertValidExtension(UploadedFile $file): void
    {
        $allowed = ['pdf', 'docx', 'pptx', 'xlsx'];
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, $allowed)) {
            abort(422, 'File type not allowed.');
        }
    }


    protected function uniqueSlug(string $base): string
    {
        $slug = $base ?: Str::random(8);
        $i = 1;
        while (DocumentTemplate::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}


