<?php

namespace Database\Seeders;

use App\Models\DocumentTemplate;
use App\Services\TemplateService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(TemplateService::class);

        $tmpDir = storage_path('app/tmp');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0775, true);
        }

        // Pitch Deck v1 (pptx placeholder)
        $pitchTmp = $tmpDir . '/pitch-deck-v1.pptx';
        file_put_contents($pitchTmp, 'PITCH_DECK_V1');
        $pitchFile = new UploadedFile($pitchTmp, 'pitch-deck-v1.pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', null, true);
        $service->createTemplate('Pitch Deck', 'Template presentasi pitch deck', $pitchFile, 1);

        // Laporan Keuangan v1 (xlsx placeholder)
        $lapTmp = $tmpDir . '/laporan-keuangan-v1.xlsx';
        file_put_contents($lapTmp, 'LAPORAN_KEUANGAN_V1');
        $lapFile = new UploadedFile($lapTmp, 'laporan-keuangan-v1.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
        $service->createTemplate('Laporan Keuangan', 'Template laporan keuangan', $lapFile, 1);
    }
}


