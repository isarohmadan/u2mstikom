<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Kewirausahaan', 'description' => 'Diskusi umum seputar kewirausahaan dan bisnis'],
            ['name' => 'Startup', 'description' => 'Topik mengenai rintisan usaha dan pertumbuhan startup'],
            ['name' => 'UMKM', 'description' => 'Usaha Mikro, Kecil, dan Menengah'],
            ['name' => 'Manajemen Bisnis', 'description' => 'Strategi, perencanaan, dan operasional bisnis'],
            ['name' => 'Pemasaran', 'description' => 'Marketing, brand, dan strategi pertumbuhan'],
            ['name' => 'Keuangan', 'description' => 'Pendanaan, arus kas, akuntansi, dan perpajakan'],
            ['name' => 'Inovasi Produk', 'description' => 'Riset pasar, pengembangan, dan validasi produk'],
            ['name' => 'Legal & Perizinan', 'description' => 'Aspek hukum, izin usaha, dan kepatuhan'],
            ['name' => 'Sumber Daya Manusia', 'description' => 'Tim, rekrutmen, budaya kerja, dan kepemimpinan'],
            ['name' => 'Panduan', 'description' => 'Tutorial dan best practice kewirausahaan'],
            ['name' => 'Tanya Jawab', 'description' => 'Pertanyaan dan jawaban terkait bisnis'],
        ];

        foreach ($categories as $cat) {
            $slug = Str::slug($cat['name']);
            // Ensure unique slug if it already exists
            $base = $slug; $i = 1;
            while (Category::where('slug', $slug)->exists()) {
                $slug = $base.'-'.$i++;
            }

            Category::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'] ?? null,
                ]
            );
        }
    }
}


