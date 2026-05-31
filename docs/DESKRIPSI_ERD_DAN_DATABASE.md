# Deskripsi ERD dan Basis Data Konseptual Sistem KMS/LMS

Dokumen ini berisi spesifikasi tekstual mengenai entitas, atribut, relasi, serta pemetaan basis data konseptual untuk sistem Knowledge Management System (KMS) / Learning Management System (LMS) Anda.

---

## 1. Spesifikasi Entity Relationship Diagram (ERD)

Bagian ini menjabarkan entitas utama, atribut kunci, dan hubungan antar entitas yang membentuk struktur basis data sistem.

### A. Entitas dan Atribut

Berikut adalah daftar entitas (tabel) beserta atribut utamanya:

1.  **Users (Pengguna)**
    *   **Deskripsi**: Menyimpan data seluruh pengguna aplikasi.
    *   **Atribut**:
        *   `id`: Primary Key (Identitas unik pengguna).
        *   `name`: Nama lengkap pengguna.
        *   `email`: Alamat email (unik).
        *   `password`: Kata sandi terenkripsi.
        *   `bookmarks`: Data JSON yang menyimpan daftar konten favorit pengguna.
        *   `created_at`: Tanggal pendaftaran.

2.  **Roles (Peran)**
    *   **Deskripsi**: Mengelola hak akses dan tingkatan pengguna.
    *   **Atribut**:
        *   `id`: Primary Key.
        *   `name`: Nama peran (misal: Administrator, Pengurus, Anggota).
        *   `guard_name`: Lingkup autentikasi (web/api).

3.  **Categories (Kategori)**
    *   **Deskripsi**: Pengelompokan bidang ilmu atau topik.
    *   **Atribut**:
        *   `id`: Primary Key.
        *   `name`: Nama kategori.
        *   `slug`: Versi URL-friendly dari nama kategori.
        *   `description`: Penjelasan singkat kategori.

4.  **Topics (Topik Diskusi)**
    *   **Deskripsi**: Thread diskusi atau pertanyaan yang dibuat pengguna.
    *   **Atribut**:
        *   `id`: Primary Key.
        *   `title`: Judul topik.
        *   `content`: Isi detail pertanyaan/diskusi.
        *   `status`: Status moderasi (submitted, approved, rejected).
        *   `tags`: Label atau tagar terkait (JSON).
        *   `user_id`: Foreign Key (Pembuat topik).
        *   `category_id`: Foreign Key (Kategori topik).

5.  **Answers (Jawaban)**
    *   **Deskripsi**: Tanggapan atau solusi atas sebuah topik diskusi.
    *   **Atribut**:
        *   `id`: Primary Key.
        *   `content`: Isi jawaban.
        *   `is_verified`: Penanda apakah jawaban ini solusi yang benar.
        *   `topic_id`: Foreign Key (Topik yang dijawab).
        *   `user_id`: Foreign Key (Penjawab).

6.  **Lessons (Materi Pembelajaran)**
    *   **Deskripsi**: Modul materi e-learning.
    *   **Atribut**:
        *   `id`: Primary Key.
        *   `title`: Judul materi.
        *   `file_path`: Lokasi file materi (PDF/Doc).
        *   `is_published`: Status publikasi materi.
        *   `category_id`: Foreign Key (Kategori materi).
        *   `created_by`: Foreign Key (Instruktur pengunggah).

7.  **Quizzes (Kuis)**
    *   **Deskripsi**: Evaluasi pembelajaran.
    *   **Atribut**:
        *   `id`: Primary Key.
        *   `title`: Judul kuis.
        *   `passing_score`: Nilai ambang batas lulus.
        *   `lesson_id`: Foreign Key (Materi terkait kuis).

8.  **QuizQuestions (Pertanyaan Kuis)**
    *   **Deskripsi**: Butir soal dalam sebuah kuis.
    *   **Atribut**:
        *   `id`: Primary Key.
        *   `question`: Teks pertanyaan.
        *   `options`: Pilihan jawaban (a, b, c, d).
        *   `correct_answer`: Kunci jawaban.
        *   `quiz_id`: Foreign Key.

9.  **DocumentTemplates (Dokumen Templat)**
    *   **Deskripsi**: Metadata templat dokumen organisasi.
    *   **Atribut**:
        *   `id`: Primary Key.
        *   `name`: Nama templat.
        *   `latest_version_id`: ID versi file terakhir.

10. **DocumentTemplateVersions (Versi Dokumen)**
    *   **Deskripsi**: File fisik revisi dokumen.
    *   **Atribut**:
        *   `id`: Primary Key.
        *   `file_path`: Lokasi file.
        *   `version_number`: Nomor versi (1, 2, dst).
        *   `template_id`: Foreign Key.

### B. Relasi Antar Entitas

Hubungan yang terbentuk antar tabel dalam sistem:

1.  **Users - Roles**: *Many-to-Many*. Satu pengguna bisa memiliki banyak peran, satu peran bisa dimiliki banyak pengguna.
2.  **Users - Topics**: *One-to-Many*. Satu pengguna dapat membuat banyak topik diskusi.
3.  **Topics - Categories**: *Many-to-Many*. Satu topik bisa masuk ke dalam beberapa kategori.
4.  **Topics - Answers**: *One-to-Many*. Satu topik diskusi memiliki banyak balasan/jawaban.
5.  **Lessons - Quizzes**: *One-to-Many*. Satu materi pelajaran dapat memiliki beberapa kuis evaluasi.
6.  **Quizzes - Users (Attempts)**: *Many-to-Many* (via tabel `user_quiz_attempts`). Banyak pengguna mengerjakan banyak kuis.
7.  **Users - Lessons (Progress)**: *Many-to-Many* (via tabel `user_lesson_progress`). Banyak pengguna mengakses banyak materi.
8.  **Templates - Versions**: *One-to-Many*. Satu dokumen templat memiliki riwayat banyak versi file.

---

## 2. Spesifikasi Basis Data Konseptual

Bagian ini menjelaskan aliran data dari sudut pandang konseptual: apa yang menjadi input (sumber data) dan apa yang menjadi output (informasi yang dihasilkan).

### A. Input Sistem (Data Pendukung)
Data mentah yang dimasukkan oleh pengguna atau administrator sebagai bahan bakar operasional sistem:

1.  **Registrasi Pengguna**: Data diri nama, email, dan password untuk pembuatan akun.
2.  **Konten Materi**: File-file materi pembelajaran (PDF, Dokumen) yang diunggah instruktur.
3.  **Data Soal & Kunci Jawaban**: Pertanyaan kuis beserta opsi dan kunci jawabannya.
4.  **Pertanyaan Forum**: Teks pertanyaan atau masalah yang diposting anggota di forum.
5.  **Tanggapan Forum**: Solusi atau komentar yang ditulis oleh ahli atau sesama anggota.
6.  **File Dokumen Templat**: Dokumen standar operasional atau formulir yang diunggah untuk dibagikan.

### B. Output Sistem (Informasi Hasil Olahan)
Informasi berguna yang disajikan kembali oleh sistem kepada pengguna setelah data diproses:

1.  **Knowledge Base (Solusi Terverifikasi)**: Daftar masalah dan solusi yang sudah terbukti benar di forum, dapat dicari kembali.
2.  **Laporan Progres Belajar**: Statistik persentase penyelesaian materi oleh setiap anggota.
3.  **Skor Akhir & Nilai**: Hasil perhitungan otomatis dari pengerjaan kuis (Lulus/Tidak Lulus).
4.  **Riwayat Dokumen**: Daftar log versi dokumen dari yang terlama hingga terbaru.
5.  **Feed Personalisasi**: Daftar topik atau materi yang relevan berdasarkan kategori yang diikuti pengguna.
6.  **Koleksi Favorit**: Daftar konten "Saved Topics" atau "Saved Lessons" milik pengguna pribadi.

---
*Dokumen ini dibuat berdasarkan struktur aktual kode program dan basis data sistem KMS/LMS Anda.*
