<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Topics;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Topik-topik yang relevan dengan UKM Unit Usaha Mahasiswa
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();
        $staffAdmins = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['administrator', 'pengurus']);
        })->get();
        $regularUsers = User::whereHas('roles', function($q) {
            $q->where('name', 'anggota');
        })->get();
        
        // Get all categories
        $categories = Category::all();
        
        if ($users->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('Users or Categories not found. Please run UserSeeder and CategorySeeder first.');
            return;
        }

        $topics = [
            // === KEWIRAUSAHAAN ===
            [
                'title' => 'Panduan Memulai Usaha Mahasiswa: Dari Ide ke Produk',
                'content' => 'Sebagai mahasiswa di UKM Unit Usaha, langkah pertama memulai usaha adalah menemukan ide yang relevan dengan keahlian dan kebutuhan pasar kampus. Mulai dengan riset sederhana: survei teman-teman tentang kebutuhan mereka, cari gap di pasar lokal, dan validasi ide dengan MVP sederhana. Pastikan untuk memanfaatkan jaringan kampus dan dosen pembimbing.',
                'status' => 'approved',
                'category' => 'Kewirausahaan',
                'tags' => ['kewirausahaan', 'ide-bisnis', 'mahasiswa'],
                'view_count' => 285,
                'is_locked' => false,
                'is_edited' => false,
            ],
            [
                'title' => 'Cara Menyusun Business Plan untuk Kompetisi Bisnis Mahasiswa',
                'content' => 'Menyusun business plan yang solid adalah kunci sukses di kompetisi bisnis mahasiswa seperti PMW, KBMI, atau kompetisi internal kampus. Dokumen harus mencakup: Executive Summary, Analisis Pasar, Strategi Pemasaran, Proyeksi Keuangan, dan Tim. Gunakan data riil dan tunjukkan keunikan value proposition produk kalian.',
                'status' => 'approved',
                'category' => 'Kewirausahaan',
                'tags' => ['business-plan', 'kompetisi', 'hibah'],
                'view_count' => 356,
                'is_locked' => false,
                'is_edited' => true,
            ],
            [
                'title' => 'Peluang Bisnis Digital untuk Mahasiswa di Era AI',
                'content' => 'Dengan kemajuan AI dan teknologi, mahasiswa memiliki peluang besar untuk membangun bisnis digital. Beberapa ide: jasa pembuatan konten AI, kursus online, dropshipping dengan automation, affiliate marketing, dan SaaS tools. Manfaatkan skill coding, desain, atau marketing yang dipelajari di kampus.',
                'status' => 'approved',
                'category' => 'Kewirausahaan',
                'tags' => ['digital', 'ai', 'peluang-bisnis'],
                'view_count' => 198,
                'is_locked' => false,
                'is_edited' => false,
            ],

            // === UMKM ===
            [
                'title' => 'Kolaborasi UKM dengan UMKM Lokal: Studi Kasus Bali',
                'content' => 'UKM Unit Usaha Mahasiswa STIKOM Bali telah menjalin kerjasama dengan beberapa UMKM lokal di Denpasar dan sekitarnya. Program magang dan praktik kerja lapangan memungkinkan mahasiswa belajar langsung dari pelaku usaha. Topik ini membahas bagaimana mahasiswa bisa membantu digitalisasi UMKM sekaligus belajar manajemen bisnis.',
                'status' => 'approved',
                'category' => 'UMKM',
                'tags' => ['kolaborasi', 'umkm', 'magang'],
                'view_count' => 167,
                'is_locked' => false,
                'is_edited' => false,
            ],
            [
                'title' => 'Program Pendampingan UMKM oleh Mahasiswa',
                'content' => 'Salah satu program unggulan UKM Unit Usaha adalah pendampingan UMKM di wilayah kampus. Mahasiswa membantu pembuatan website, sosial media marketing, dan pembukuan digital. Program ini berjalan sejak 2023 dan telah membantu 15+ UMKM.',
                'status' => 'approved',
                'category' => 'UMKM',
                'tags' => ['pendampingan', 'umkm', 'pengabdian'],
                'view_count' => 134,
                'is_locked' => false,
                'is_edited' => true,
            ],

            // === PEMASARAN ===
            [
                'title' => 'Strategi Social Media Marketing untuk Produk Mahasiswa',
                'content' => 'Pemasaran produk mahasiswa di media sosial membutuhkan strategi khusus dengan budget terbatas. Tips: 1) Manfaatkan Instagram Reels dan TikTok untuk reach organic, 2) Buat konten edukatif terkait produk, 3) Kolaborasi dengan micro-influencer kampus, 4) Gunakan fitur shop di platform sosmed.',
                'status' => 'approved',
                'category' => 'Pemasaran',
                'tags' => ['sosmed', 'marketing', 'instagram'],
                'view_count' => 412,
                'is_locked' => false,
                'is_edited' => false,
            ],
            [
                'title' => 'Membuat Konten TikTok yang Viral untuk Promosi Produk',
                'content' => 'TikTok adalah platform ideal untuk promosi produk mahasiswa karena algoritma yang mendukung akun baru. Tips konten viral: hook 3 detik pertama, storytelling, trending audio, dan CTA yang jelas. Contoh format: behind the scenes produksi, testimoni customer, day in the life entrepreneur mahasiswa.',
                'status' => 'approved',
                'category' => 'Pemasaran',
                'tags' => ['tiktok', 'viral', 'konten'],
                'view_count' => 523,
                'is_locked' => false,
                'is_edited' => false,
            ],
            [
                'title' => 'Email Marketing untuk Bisnis Kecil: Tools Gratis yang Bisa Digunakan',
                'content' => 'Email marketing masih efektif untuk nurturing leads dan repeat customers. Tools gratis: Mailchimp (2500 emails/bulan), Sendinblue (300 emails/hari), MailerLite (1000 subscribers). Tips: buat welcome series, newsletter mingguan, dan promo khusus subscriber.',
                'status' => 'submitted',
                'category' => 'Pemasaran',
                'tags' => ['email', 'tools', 'marketing'],
                'view_count' => 78,
                'is_locked' => false,
                'is_edited' => false,
            ],

            // === KEUANGAN ===
            [
                'title' => 'Mengelola Keuangan Bisnis Mahasiswa dengan Excel',
                'content' => 'Untuk bisnis mahasiswa dengan transaksi < 100/bulan, Excel sudah cukup untuk pencatatan keuangan. Template yang diperlukan: Arus Kas, Buku Penjualan, Buku Pembelian, dan Laporan Laba Rugi. Lampiran template bisa didownload di dokumen yang dilampirkan.',
                'status' => 'approved',
                'category' => 'Keuangan',
                'tags' => ['keuangan', 'excel', 'pembukuan'],
                'view_count' => 289,
                'is_locked' => false,
                'is_edited' => false,
            ],
            [
                'title' => 'Cara Menghitung HPP dan Menentukan Harga Jual',
                'content' => 'Harga Pokok Produksi (HPP) adalah dasar penentuan harga jual. Rumus: HPP = Biaya Bahan + Biaya Tenaga Kerja + Overhead. Untuk harga jual, tambahkan margin yang realistis (20-50% untuk produk fisik, 50-100% untuk jasa). Pertimbangkan juga harga kompetitor dan willingness to pay target market.',
                'status' => 'approved',
                'category' => 'Keuangan',
                'tags' => ['hpp', 'harga', 'akuntansi'],
                'view_count' => 345,
                'is_locked' => false,
                'is_edited' => true,
            ],
            [
                'title' => 'Sumber Pendanaan untuk Usaha Mahasiswa: Hibah dan Modal',
                'content' => 'Mahasiswa bisa mengakses berbagai sumber pendanaan: 1) Hibah PMW Kemendikbud, 2) KBMI (Kompetisi Bisnis Mahasiswa Indonesia), 3) Program Kreativitas Mahasiswa, 4) Hibah internal kampus, 5) Investor malaikat/angel investor. Tips: siapkan pitch deck yang solid dan perlihatkan traction awal.',
                'status' => 'approved',
                'category' => 'Keuangan',
                'tags' => ['pendanaan', 'hibah', 'investor'],
                'view_count' => 267,
                'is_locked' => false,
                'is_edited' => false,
            ],

            // === STARTUP ===
            [
                'title' => 'Kisah Sukses Startup Mahasiswa: Dari Kampus ke Pasar',
                'content' => 'Beberapa startup sukses yang dirintis mahasiswa: Gojek (Nadiem dari Harvard), Tokopedia (William dari ITB), dan Traveloka (Ferry dari Purdue). Pelajaran: 1) Solve real problem, 2) Start small, think big, 3) Build strong team, 4) Iterate based on feedback.',
                'status' => 'approved',
                'category' => 'Startup',
                'tags' => ['startup', 'inspirasi', 'sukses'],
                'view_count' => 178,
                'is_locked' => false,
                'is_edited' => false,
            ],
            [
                'title' => 'Lean Startup Methodology untuk Bisnis Mahasiswa',
                'content' => 'Metodologi Lean Startup cocok untuk mahasiswa dengan resource terbatas. Cycle: Build → Measure → Learn. Buat MVP, test dengan customer riil, kumpulkan feedback, iterate. Jangan bangun produk sempurna, fokus pada validated learning.',
                'status' => 'approved',
                'category' => 'Startup',
                'tags' => ['lean', 'mvp', 'metodologi'],
                'view_count' => 156,
                'is_locked' => false,
                'is_edited' => false,
            ],

            // === INOVASI PRODUK ===
            [
                'title' => 'Design Thinking untuk Pengembangan Produk',
                'content' => 'Design Thinking adalah metode sistematis untuk inovasi produk. 5 Tahap: 1) Empathize - pahami user, 2) Define - tentukan problem statement, 3) Ideate - brainstorm solusi, 4) Prototype - buat model sederhana, 5) Test - uji dengan user riil. Metode ini bisa diterapkan untuk produk fisik maupun digital.',
                'status' => 'approved',
                'category' => 'Inovasi Produk',
                'tags' => ['design-thinking', 'inovasi', 'produk'],
                'view_count' => 201,
                'is_locked' => false,
                'is_edited' => false,
            ],
            [
                'title' => 'Ide Produk Kreatif Berbahan Lokal Bali',
                'content' => 'Bali kaya akan bahan lokal yang bisa diolah menjadi produk bernilai tinggi: kerajinan bambu, tenun endek digital, olahan arak Bali, coconut-based products, dan merchandise budaya. UKM Unit Usaha telah mengembangkan beberapa produk berbahan lokal dengan desain modern.',
                'status' => 'approved',
                'category' => 'Inovasi Produk',
                'tags' => ['lokal', 'bali', 'kreatif'],
                'view_count' => 234,
                'is_locked' => false,
                'is_edited' => true,
            ],

            // === MANAJEMEN BISNIS ===
            [
                'title' => 'Time Management untuk Mahasiswa Entrepreneur',
                'content' => 'Menyeimbangkan kuliah dan bisnis membutuhkan manajemen waktu yang baik. Tips: 1) Gunakan time blocking, 2) Prioritas dengan Eisenhower Matrix, 3) Delegate tugas operasional, 4) Manfaatkan waktu jeda antar kelas, 5) Set boundaries - ada waktu khusus untuk bisnis dan kuliah.',
                'status' => 'approved',
                'category' => 'Manajemen Bisnis',
                'tags' => ['manajemen-waktu', 'produktivitas', 'balance'],
                'view_count' => 312,
                'is_locked' => false,
                'is_edited' => false,
            ],
            [
                'title' => 'Membangun Tim Bisnis dari Sesama Mahasiswa',
                'content' => 'Tim adalah aspek krusial startup mahasiswa. Tips rekrutmen: 1) Cari co-founder dengan skill komplementer, 2) Tetapkan peran dan tanggung jawab jelas, 3) Buat agreement tertulis tentang equity/bagi hasil, 4) Komunikasi rutin minimal mingguan, 5) Jangan rekrut teman dekat jika tidak qualified.',
                'status' => 'approved',
                'category' => 'Manajemen Bisnis',
                'tags' => ['tim', 'co-founder', 'rekrutmen'],
                'view_count' => 189,
                'is_locked' => false,
                'is_edited' => false,
            ],

            // === SUMBER DAYA MANUSIA ===
            [
                'title' => 'Rekrut Anggota Tim dengan Skill yang Tepat',
                'content' => 'Untuk bisnis mahasiswa, tim ideal minimal: 1 orang product/operasional, 1 orang marketing/sales, dan 1 orang keuangan/admin. Tidak harus full-time, bisa part-time atau project-based. Platform rekrut: grup mahasiswa, career center kampus, atau LinkedIn.',
                'status' => 'approved',
                'category' => 'Sumber Daya Manusia',
                'tags' => ['sdm', 'rekrutmen', 'skill'],
                'view_count' => 145,
                'is_locked' => false,
                'is_edited' => false,
            ],

            // === LEGAL & PERIZINAN ===
            [
                'title' => 'Legalitas Usaha Mahasiswa: CV, PT, atau Perorangan?',
                'content' => 'Untuk usaha mahasiswa tahap awal, bentuk usaha perorangan atau CV sudah cukup. Keuntungan CV: mudah didirikan (2-3 hari), biaya terjangkau (1-3 juta), bisa buka rekening usaha. PT diperlukan jika ingin mencari investor atau mengikuti tender.',
                'status' => 'approved',
                'category' => 'Legal & Perizinan',
                'tags' => ['legal', 'cv', 'perizinan'],
                'view_count' => 178,
                'is_locked' => false,
                'is_edited' => false,
            ],
            [
                'title' => 'Cara Daftar Merek Dagang untuk Produk Mahasiswa',
                'content' => 'Melindungi merek produk lewat pendaftaran di DJKI (Dirjen Kekayaan Intelektual). Biaya pendaftaran merek untuk UMKM/mahasiswa: Rp 500.000 - 800.000. Proses: 1) Cek ketersediaan nama, 2) Siapkan logo, 3) Daftar online di dgip.go.id, 4) Tunggu pemeriksaan (6-12 bulan).',
                'status' => 'submitted',
                'category' => 'Legal & Perizinan',
                'tags' => ['merek', 'haki', 'legal'],
                'view_count' => 89,
                'is_locked' => false,
                'is_edited' => false,
            ],

            // === PANDUAN ===
            [
                'title' => 'Panduan Mengikuti Kompetisi Bisnis Tingkat Nasional',
                'content' => 'Langkah persiapan kompetisi bisnis: 1) Pilih kompetisi yang sesuai (PMW, KBMI, NSC), 2) Baca guideline dengan teliti, 3) Susun tim dan bagi tugas, 4) Buat timeline mundur dari deadline, 5) Minta review dari dosen/mentor, 6) Latihan presentasi/pitching.',
                'status' => 'approved',
                'category' => 'Panduan',
                'tags' => ['panduan', 'kompetisi', 'tips'],
                'view_count' => 267,
                'is_locked' => false,
                'is_edited' => false,
            ],
            [
                'title' => 'Cara Membuat Pitch Deck yang Menarik untuk Investor',
                'content' => 'Pitch deck yang efektif: 10-15 slides mencakup: Problem, Solution, Market Size, Business Model, Traction, Competition, Team, Financials, Ask. Tips: gunakan visual menarik, data konkret, dan cerita yang compelling. Template bisa diakses di lampiran.',
                'status' => 'approved',
                'category' => 'Panduan',
                'tags' => ['pitch-deck', 'investor', 'presentasi'],
                'view_count' => 345,
                'is_locked' => false,
                'is_edited' => true,
            ],

            // === TANYA JAWAB ===
            [
                'title' => 'Tanya: Bagaimana Cara Memulai Bisnis dengan Modal < 1 Juta?',
                'content' => 'Untuk memulai bisnis dengan modal terbatas, fokus pada bisnis jasa atau dropship. Ide: jasa desain grafis, social media management, reseller produk, tutoring online, atau content creation. Modal utama adalah skill dan waktu, bukan uang.',
                'status' => 'approved',
                'category' => 'Tanya Jawab',
                'tags' => ['tanya', 'modal-kecil', 'pemula'],
                'view_count' => 456,
                'is_locked' => false,
                'is_edited' => true,
            ],
            [
                'title' => 'Tanya: Apakah Bisa Bisnis Sambil Kuliah Full-time?',
                'content' => 'Banyak mahasiswa sukses berbisnis sambil kuliah. Kuncinya: 1) Pilih bisnis yang fleksibel, 2) Automate/delegate operasional, 3) Prioritas deadline kuliah, 4) Manfaatkan weekend untuk bisnis, 5) Jangan korbankan kesehatan. Beberapa senior UKM berhasil lulus cum laude sambil berbisnis.',
                'status' => 'approved',
                'category' => 'Tanya Jawab',
                'tags' => ['tanya', 'kuliah', 'balance'],
                'view_count' => 389,
                'is_locked' => false,
                'is_edited' => false,
            ],
            [
                'title' => 'Tanya: Marketplace Mana yang Cocok untuk Produk Mahasiswa?',
                'content' => 'Pilihan marketplace tergantung jenis produk: Tokopedia/Shopee untuk produk fisik massal, Instagram/TikTok Shop untuk fashion/lifestyle, Gumroad/Lemon Squeezy untuk digital product. Untuk jasa, gunakan Fastwork atau direct via social media.',
                'status' => 'submitted',
                'category' => 'Tanya Jawab',
                'tags' => ['tanya', 'marketplace', 'jualan'],
                'view_count' => 67,
                'is_locked' => false,
                'is_edited' => false,
            ],
        ];

        foreach ($topics as $index => $topicData) {
            // Select random user (prefer regular users for realism)
            $randomUser = $users->count() > 2 ? ($regularUsers->isNotEmpty() ? $regularUsers->random() : $users->random()) : $users->first();
            
            // Find category
            $category = $categories->firstWhere('name', $topicData['category']);
            
            // Generate unique slug
            $slug = Str::slug($topicData['title']);
            $baseSlug = $slug;
            $counter = 1;
            while (Topics::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            
            // Determine approved_by based on status
            $approvedBy = null;
            if ($topicData['status'] === 'approved') {
                $approvedBy = $staffAdmins->isNotEmpty() ? $staffAdmins->random()->id : null;
            }
            
            // Determine edited_by if is_edited is true
            $editedBy = null;
            if ($topicData['is_edited']) {
                $editedBy = $randomUser->id;
            }
            
            // Create topic
            $topic = Topics::create([
                'user_id' => $randomUser->id,
                'title' => $topicData['title'],
                'slug' => $slug,
                'content' => $topicData['content'],
                'status' => $topicData['status'],
                'approved_by' => $approvedBy,
                'category_id' => $category ? $category->id : null,
                'tags' => $topicData['tags'],
                'is_locked' => $topicData['is_locked'],
                'is_edited' => $topicData['is_edited'],
                'edited_by' => $editedBy,
                'view_count' => $topicData['view_count'],
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => $topicData['is_edited'] ? now()->subDays(rand(1, 30)) : now()->subDays(rand(1, 60)),
            ]);
        }

        $this->command->info('Created ' . count($topics) . ' topics successfully!');
    }
}
