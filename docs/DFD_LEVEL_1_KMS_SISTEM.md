# 📊 Data Flow Diagram (DFD) Level 1 - Sistem Knowledge Management System (KMS)

Dokumen ini berisi DFD Level 1 untuk setiap proses dari DFD Level 0, dengan detail sub-proses yang lebih spesifik. Setiap proses dibuat terpisah untuk memudahkan pemahaman.

**Format:** Mermaid.js - Dapat digunakan di GitHub, GitLab, Mermaid Live Editor, dan dokumentasi online.

---

## 📋 Daftar DFD Level 1

1. [P 1.0 Login (Authentication)](#p-10-login-authentication)
2. [P 2.0 Dashboard](#p-20-dashboard)
3. [P 3.0 Forum Diskusi](#p-30-forum-diskusi)
4. [P 4.0 Topik Saya](#p-40-topik-saya)
5. [P 5.0 Pembelajaran](#p-50-pembelajaran)
6. [P 6.0 Dokumen Template](#p-60-dokumen-template)
7. [P 7.0 Kategori](#p-70-kategori)
8. [P 8.0 Favorite Saya](#p-80-favorite-saya)
9. [P 9.0 Manajemen Pengguna](#p-90-manajemen-pengguna)
10. [P 10.0 Manajemen Peran](#p-100-manajemen-peran)
11. [P 11.0 Pengaturan Akun](#p-110-pengaturan-akun)

---

## P 1.0 Login (Authentication)

DFD Level 1 untuk proses autentikasi dan login user.

```mermaid
flowchart TB
    %% External Entities
    Admin["👨‍💼 Administrator"]
    Pengurus["👩‍💼 Pengurus"]
    Anggota["👤 Anggota"]
    
    %% Sub-Processes
    P11(("1.1<br/>Masukan email<br/>& password"))
    P12(("1.2<br/>Verifikasi kredensial"))
    P13(("1.3<br/>Generate session<br/>& role"))
    
    %% Data Store
    DS1[("D1: Database Users<br/>(users)"]
    DS2[("D2: Database Roles<br/>(roles<br/>model_has_roles)"]
    
    %% Data Flows - Input dari External Entities
    Admin -->|kredensial_login| P11
    Pengurus -->|kredensial_login| P11
    Anggota -->|kredensial_login| P11
    
    %% Data Flows - Antara Sub-Processes
    P11 -->|email, password| P12
    P12 -->|user_valid| P13
    P12 -->|pesan_error| P11
    P13 -->|session_user_role| P11
    
    %% Data Flows - Data Store
    P12 -->|read_user_data| DS1
    P12 -->|read_role_data| DS2
    DS1 -->|user_data| P12
    DS2 -->|role_permission_data| P12
    
    %% Data Flows - Output ke External Entities
    P13 -->|session_user_role| Admin
    P13 -->|session_user_role| Pengurus
    P13 -->|session_user_role| Anggota
    P12 -->|pesan_error| Admin
    P12 -->|pesan_error| Pengurus
    P12 -->|pesan_error| Anggota
    
    %% Styling
    style Admin fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Pengurus fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Anggota fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style P11 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P12 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P13 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style DS1 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style DS2 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
```

### Penjelasan Sub-Proses:

**1.1 Masukan email & password**
- Menerima input email dan password dari user (Administrator, Pengurus, atau Anggota)
- Validasi format input (email harus valid, password tidak kosong)
- Mengirimkan kredensial ke proses 1.2 untuk verifikasi
- Menerima feedback dari proses 1.2 dan 1.3 (berhasil/gagal)

**1.2 Verifikasi kredensial**
- Menerima email dan password dari proses 1.1
- Query ke database (D1: Database Users) untuk mencari user dengan email tersebut
- Query ke database (D2: Database Roles) untuk mendapatkan role dan permission
- Verifikasi password menggunakan hash comparison
- Cek status user (aktif/tidak aktif)
- Jika valid, kirim ke proses 1.3
- Jika tidak valid, kirim error ke proses 1.1

**1.3 Generate session & role**
- Menerima data user yang valid dari proses 1.2
- Generate session token
- Simpan session dengan role dan permission data
- Redirect ke dashboard sesuai role
- Mengirimkan session dan role ke proses 1.1 dan external entities

---

## P 2.0 Dashboard

DFD Level 1 untuk proses dashboard yang menampilkan statistik dan informasi umum.

```mermaid
flowchart TB
    %% External Entities
    Admin["👨‍💼 Administrator"]
    Pengurus["👩‍💼 Pengurus"]
    Anggota["👤 Anggota"]
    
    %% Sub-Processes
    P21(("2.1<br/>Request<br/>dashboard"))
    P22(("2.2<br/>Kumpulkan<br/>statistik"))
    P23(("2.3<br/>Format data<br/>dashboard"))
    P24(("2.4<br/>Tampilkan<br/>dashboard"))
    
    %% Data Stores
    DS1[("D1: Database Users<br/>(users)"]
    DS3[("D3: Database Topics<br/>(topics)"]
    DS6[("D6: Database Templates<br/>(document_templates<br/>document_template_logs)"]
    
    %% Data Flows - Input dari External Entities
    Admin -->|request_dashboard| P21
    Pengurus -->|request_dashboard| P21
    Anggota -->|request_dashboard| P21
    
    %% Data Flows - Antara Sub-Processes
    P21 -->|request| P22
    P22 -->|data_statistik| P23
    P23 -->|data_dashboard| P24
    
    %% Data Flows - Data Store
    P22 -->|read_user_statistics| DS1
    P22 -->|read_topic_statistics| DS3
    P22 -->|read_template_statistics| DS6
    DS1 -->|user_statistics| P22
    DS3 -->|topic_statistics| P22
    DS6 -->|template_statistics| P22
    
    %% Data Flows - Output ke External Entities
    P24 -->|dashboard_data| Admin
    P24 -->|dashboard_data| Pengurus
    P24 -->|dashboard_data| Anggota
    
    %% Styling
    style Admin fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Pengurus fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Anggota fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style P21 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P22 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P23 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P24 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style DS1 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style DS3 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style DS6 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
```

### Penjelasan Sub-Proses:

**2.1 Request dashboard**
- Menerima request dashboard dari semua role (Administrator, Pengurus, Anggota)
- Validasi session user
- Mengirimkan request ke proses 2.2

**2.2 Kumpulkan statistik**
- Query statistik user (total anggota, total pengurus) dari D1: Database Users
- Query statistik topik (total topik, topik mingguan) dari D3: Database Topics
- Query statistik template (total download, download mingguan) dari D6: Database Templates
- Query top contributors berdasarkan jumlah topik
- Query pengumuman terbaru
- Mengirimkan semua data statistik ke proses 2.3

**2.3 Format data dashboard**
- Format data statistik menjadi format yang siap ditampilkan
- Filter data berdasarkan permission user
- Menyiapkan data chart (weekly topics, weekly downloads)
- Mengirimkan data yang sudah diformat ke proses 2.4

**2.4 Tampilkan dashboard**
- Render view dashboard dengan data yang sudah diformat
- Tampilkan sesuai dengan permission user
- Mengirimkan dashboard data ke semua external entities

---

## P 3.0 Forum Diskusi

DFD Level 1 untuk proses manajemen forum diskusi dan topik.

```mermaid
flowchart TB
    %% External Entities
    Admin["👨‍💼 Administrator"]
    Pengurus["👩‍💼 Pengurus"]
    Anggota["👤 Anggota"]
    
    %% Sub-Processes
    P31(("3.1<br/>Input data<br/>topik"))
    P32(("3.2<br/>Validasi data<br/>topik"))
    P33(("3.3<br/>Simpan data<br/>topik"))
    P34(("3.4<br/>Approval<br/>topik"))
    P35(("3.5<br/>Tampilkan daftar<br/>topik"))
    P36(("3.6<br/>Tampilkan detail<br/>topik"))
    
    %% Data Stores
    DS3[("D3: Database Topics<br/>(topics)"]
    DS4[("D4: Database Answers<br/>(answers<br/>answer_comments)"]
    DS5[("D5: Database Categories<br/>(categories)"]
    
    %% Data Flows - Input dari External Entities
    Admin -->|data_topik| P31
    Admin -->|data_approval| P34
    Admin -->|info_topik| P35
    Pengurus -->|data_topik| P31
    Pengurus -->|data_approval| P34
    Pengurus -->|info_topik| P35
    Anggota -->|data_topik| P31
    Anggota -->|info_topik| P35
    
    %% Data Flows - Antara Sub-Processes
    P31 -->|data_topik| P32
    P32 -->|data_valid| P33
    P32 -->|pesan_error| P31
    P33 -->|data_topik| P34
    P34 -->|data_topik| P35
    P35 -->|request_detail| P36
    
    %% Data Flows - Data Store
    P33 -->|write_topic| DS3
    P34 -->|update_topic| DS3
    DS3 -->|topic_data| P35
    DS3 -->|topic_data| P36
    DS4 -->|answer_data| P36
    DS5 -->|category_data| P31
    DS5 -->|category_data| P35
    
    %% Data Flows - Output ke External Entities
    P35 -->|list_topik| Admin
    P35 -->|list_topik| Pengurus
    P35 -->|list_topik| Anggota
    P36 -->|detail_topik| Admin
    P36 -->|detail_topik| Pengurus
    P36 -->|detail_topik| Anggota
    
    %% Styling
    style Admin fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Pengurus fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Anggota fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style P31 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P32 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P33 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P34 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P35 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P36 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style DS3 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style DS4 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style DS5 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
```

### Penjelasan Sub-Proses:

**3.1 Input data topik**
- Menerima data topik dari semua role
- Data: title, content, category_id, tags
- Query kategori dari D5: Database Categories
- Mengirimkan data ke proses 3.2 untuk validasi

**3.2 Validasi data topik**
- Validasi title (required, max 255 karakter)
- Validasi content (required, tidak kosong)
- Validasi category_id (optional, harus ada di categories)
- Jika valid, kirim ke proses 3.3
- Jika tidak valid, kirim error ke proses 3.1

**3.3 Simpan data topik**
- Generate slug dari title
- Set status = 'submitted' (untuk anggota) atau 'approved' (untuk admin/pengurus)
- Set user_id dari user yang login
- Simpan ke D3: Database Topics
- Jika perlu approval, kirim ke proses 3.4
- Jika tidak perlu, kirim langsung ke proses 3.5

**3.4 Approval topik**
- Menerima request approval dari Administrator atau Pengurus
- Update status: 'submitted' → 'approved' atau 'rejected'
- Set approved_by dengan user yang approve
- Update di D3: Database Topics
- Kirim ke proses 3.5 untuk ditampilkan

**3.5 Tampilkan daftar topik**
- Query topik dari D3: Database Topics
- Filter berdasarkan status, category, search query
- Include category, user, answer count
- Format data untuk ditampilkan
- Kirim list topik ke semua external entities

**3.6 Tampilkan detail topik**
- Query detail topik dari D3: Database Topics
- Query answers dan comments dari D4: Database Answers
- Include user, votes, verified status
- Format data untuk ditampilkan
- Kirim detail topik ke semua external entities

---

## P 4.0 Topik Saya

DFD Level 1 untuk proses manajemen topik milik user sendiri.

```mermaid
flowchart TB
    %% External Entities
    Admin["👨‍💼 Administrator"]
    Pengurus["👩‍💼 Pengurus"]
    Anggota["👤 Anggota"]
    
    %% Sub-Processes
    P41(("4.1<br/>Request topik<br/>saya"))
    P42(("4.2<br/>Query topik<br/>user"))
    P43(("4.3<br/>Edit topik<br/>saya"))
    P44(("4.4<br/>Update topik<br/>saya"))
    P45(("4.5<br/>Delete topik<br/>saya"))
    P46(("4.6<br/>Tampilkan daftar<br/>topik saya"))
    
    %% Data Store
    DS3[("D3: Database Topics<br/>(topics)"]
    
    %% Data Flows - Input dari External Entities
    Admin -->|request_topik_saya| P41
    Admin -->|data_edit_topik| P43
    Admin -->|data_delete_topik| P45
    Pengurus -->|request_topik_saya| P41
    Pengurus -->|data_edit_topik| P43
    Pengurus -->|data_delete_topik| P45
    Anggota -->|request_topik_saya| P41
    Anggota -->|data_edit_topik| P43
    Anggota -->|data_delete_topik| P45
    
    %% Data Flows - Antara Sub-Processes
    P41 -->|request| P42
    P42 -->|topik_data| P46
    P43 -->|data_edit| P44
    P44 -->|topik_updated| P42
    P45 -->|delete_request| DS3
    P45 -->|topik_deleted| P42
    
    %% Data Flows - Data Store
    P42 -->|read_user_topics| DS3
    P44 -->|update_topic| DS3
    P45 -->|delete_topic| DS3
    DS3 -->|user_topics| P42
    DS3 -->|topic_data| P46
    
    %% Data Flows - Output ke External Entities
    P46 -->|list_topik_saya| Admin
    P46 -->|list_topik_saya| Pengurus
    P46 -->|list_topik_saya| Anggota
    
    %% Styling
    style Admin fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Pengurus fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Anggota fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style P41 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P42 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P43 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P44 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P45 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P46 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style DS3 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
```

### Penjelasan Sub-Proses:

**4.1 Request topik saya**
- Menerima request dari semua role untuk melihat topik mereka sendiri
- Validasi session user
- Mengirimkan request ke proses 4.2

**4.2 Query topik user**
- Query topik dari D3: Database Topics berdasarkan user_id dari session
- Filter dan sort sesuai request
- Include category, answer count, status
- Mengirimkan data ke proses 4.6

**4.3 Edit topik saya**
- Menerima request edit topik dari user
- Validasi bahwa topik tersebut milik user yang login
- Query data topik yang akan diedit
- Mengirimkan data ke proses 4.4

**4.4 Update topik saya**
- Validasi data yang akan diupdate
- Update data topik di D3: Database Topics
- Set updated_at
- Jika status berubah, reset approval (jika diperlukan)
- Mengirimkan update ke proses 4.2

**4.5 Delete topik saya**
- Menerima request delete topik
- Validasi bahwa topik tersebut milik user yang login
- Hapus topik dari D3: Database Topics
- Hapus related data (answers, comments) jika cascade
- Mengirimkan konfirmasi ke proses 4.2

**4.6 Tampilkan daftar topik saya**
- Format data topik user untuk ditampilkan
- Include status, category, answer count
- Kirim list topik saya ke semua external entities

---

## P 5.0 Pembelajaran

DFD Level 1 untuk proses manajemen pembelajaran (lessons/e-learning).

```mermaid
flowchart TB
    %% External Entities
    Admin["👨‍💼 Administrator"]
    Pengurus["👩‍💼 Pengurus"]
    Anggota["👤 Anggota"]
    
    %% Sub-Processes
    P51(("5.1<br/>Input data<br/>pembelajaran"))
    P52(("5.2<br/>Validasi data<br/>pembelajaran"))
    P53(("5.3<br/>Upload file<br/>pembelajaran"))
    P54(("5.4<br/>Simpan data<br/>pembelajaran"))
    P55(("5.5<br/>Tampilkan daftar<br/>pembelajaran"))
    P56(("5.6<br/>Tampilkan detail<br/>pembelajaran"))
    P57(("5.7<br/>Update progress<br/>pembelajaran"))
    P58(("5.8<br/>Input data<br/>kuis"))
    P59(("5.9<br/>Validasi data<br/>kuis"))
    P510(("5.10<br/>Simpan data<br/>kuis & pertanyaan"))
    P511(("5.11<br/>Tampilkan daftar<br/>kuis"))
    P512(("5.12<br/>Ambil kuis<br/>(take quiz)"))
    P513(("5.13<br/>Submit jawaban<br/>kuis"))
    P514(("5.14<br/>Hitung nilai<br/>kuis"))
    P515(("5.15<br/>Simpan hasil<br/>kuis"))
    
    %% Data Stores
    DS7[("D7: Database Lessons<br/>(lessons<br/>lesson_progress)"]
    DS9[("D9: Database Quizzes<br/>(quizzes<br/>quiz_questions<br/>user_quiz_attempts)"]
    FS[("File Storage<br/>(storage/app/lessons/)")]
    
    %% Data Flows - Input dari External Entities
    Admin -->|data_pembelajaran| P51
    Admin -->|info_pembelajaran| P55
    Admin -->|data_kuis| P58
    Admin -->|info_kuis| P511
    Pengurus -->|data_pembelajaran| P51
    Pengurus -->|info_pembelajaran| P55
    Pengurus -->|data_kuis| P58
    Pengurus -->|info_kuis| P511
    Anggota -->|info_pembelajaran| P55
    Anggota -->|progress_pembelajaran| P57
    Anggota -->|info_kuis| P511
    Anggota -->|request_take_quiz| P512
    Anggota -->|jawaban_kuis| P513
    
    %% Data Flows - Antara Sub-Processes
    P51 -->|data_pembelajaran| P52
    P52 -->|data_valid| P53
    P52 -->|pesan_error| P51
    P53 -->|file_path| P54
    P54 -->|data_pembelajaran| P55
    P55 -->|request_detail| P56
    P57 -->|progress_data| DS7
    P58 -->|data_kuis| P59
    P59 -->|data_valid| P510
    P59 -->|pesan_error| P58
    P510 -->|kuis_tersimpan| P511
    P56 -->|request_kuis| P511
    P511 -->|request_take_quiz| P512
    P512 -->|jawaban_kuis| P513
    P513 -->|jawaban_submit| P514
    P514 -->|nilai_terhitung| P515
    
    %% Data Flows - Data Store
    P54 -->|write_lesson| DS7
    P56 -->|read_lesson| DS7
    P57 -->|update_progress| DS7
    DS7 -->|lesson_data| P55
    DS7 -->|lesson_data| P56
    P53 -->|upload_file| FS
    FS -->|download_file| P56
    P510 -->|write_quiz| DS9
    P510 -->|write_questions| DS9
    P511 -->|read_quizzes| DS9
    P512 -->|read_quiz| DS9
    P512 -->|create_attempt| DS9
    P513 -->|read_attempt| DS9
    P514 -->|read_questions| DS9
    P515 -->|write_attempt_result| DS9
    DS9 -->|quiz_data| P511
    DS9 -->|quiz_data| P512
    DS9 -->|question_data| P512
    DS9 -->|attempt_data| P513
    DS7 -->|lesson_data| P58
    
    %% Data Flows - Output ke External Entities
    P55 -->|list_pembelajaran| Admin
    P55 -->|list_pembelajaran| Pengurus
    P55 -->|list_pembelajaran| Anggota
    P56 -->|detail_pembelajaran| Admin
    P56 -->|detail_pembelajaran| Pengurus
    P56 -->|detail_pembelajaran| Anggota
    P511 -->|list_kuis| Admin
    P511 -->|list_kuis| Pengurus
    P511 -->|list_kuis| Anggota
    P512 -->|kuis_ready| Anggota
    P515 -->|hasil_kuis| Anggota
    
    %% Styling
    style Admin fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Pengurus fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Anggota fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style P51 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P52 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P53 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P54 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P55 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P56 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P57 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P58 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P59 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P510 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P511 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P512 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P513 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P514 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P515 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style DS7 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style DS9 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style FS fill:#E8F4F8,stroke:#5C8A2E,stroke-width:2px
```

### Penjelasan Sub-Proses:

**5.1 Input data pembelajaran**
- Menerima data pembelajaran dari Administrator atau Pengurus
- Data: title, description, content, file
- Mengirimkan ke proses 5.2 untuk validasi

**5.2 Validasi data pembelajaran**
- Validasi title (required, max 255 karakter)
- Validasi description (optional)
- Validasi content (optional)
- Validasi file (required untuk upload, max size, file type)
- Jika valid, kirim ke proses 5.3
- Jika tidak valid, kirim error ke proses 5.1

**5.3 Upload file pembelajaran**
- Generate unique filename
- Simpan file ke File Storage (storage/app/lessons/)
- Return file path
- Kirim file path ke proses 5.4

**5.4 Simpan data pembelajaran**
- Simpan data pembelajaran ke D7: Database Lessons
- Set created_by dari user yang login
- Set status (published/draft)
- Set created_at dan updated_at
- Kirim ke proses 5.5 untuk ditampilkan

**5.5 Tampilkan daftar pembelajaran**
- Query pembelajaran dari D7: Database Lessons
- Filter berdasarkan status (published untuk anggota)
- Include progress user (jika ada)
- Format data untuk ditampilkan
- Kirim list pembelajaran ke semua external entities

**5.6 Tampilkan detail pembelajaran**
- Query detail pembelajaran dari D7: Database Lessons
- Query progress user dari lesson_progress
- Baca file dari File Storage jika perlu download
- Format data untuk ditampilkan
- Kirim detail pembelajaran ke semua external entities

**5.7 Update progress pembelajaran**
- Menerima progress update dari Anggota
- Update atau create progress di D7: Database Lessons (lesson_progress)
- Track completion percentage
- Set last_accessed_at
- Simpan progress ke database

**5.8 Input data kuis**
- Menerima data kuis dari Administrator atau Pengurus
- Data: title, description, time_limit, passing_score, allow_retry, is_published, questions (array)
- Query lesson dari D7: Database Lessons untuk validasi lesson_id
- Mengirimkan ke proses 5.9 untuk validasi

**5.9 Validasi data kuis**
- Validasi title (required, max 255 karakter)
- Validasi description (optional)
- Validasi time_limit (optional, integer, min 1)
- Validasi passing_score (required, integer, min 0, max 100)
- Validasi allow_retry (boolean)
- Validasi is_published (boolean)
- Validasi questions (required, array, min 1 pertanyaan)
- Validasi setiap pertanyaan: question, option_a, option_b, option_c (optional), option_d (optional), correct_answer (required, in: a,b,c,d), points (optional, integer, min 1)
- Jika valid, kirim ke proses 5.10
- Jika tidak valid, kirim error ke proses 5.8

**5.10 Simpan data kuis & pertanyaan**
- Simpan data kuis ke D9: Database Quizzes (quizzes table)
- Set lesson_id, created_by dari user yang login
- Set created_at dan updated_at
- Simpan setiap pertanyaan ke D9: Database Quizzes (quiz_questions table)
- Set quiz_id, order, points (default 1)
- Kirim ke proses 5.11 untuk ditampilkan

**5.11 Tampilkan daftar kuis**
- Query kuis dari D9: Database Quizzes berdasarkan lesson_id
- Filter berdasarkan is_published (published untuk anggota)
- Include question count, time_limit, passing_score
- Format data untuk ditampilkan
- Kirim list kuis ke semua external entities

**5.12 Ambil kuis (take quiz)**
- Menerima request take quiz dari Anggota
- Query kuis dari D9: Database Quizzes
- Validasi is_published (kecuali untuk admin/pengurus)
- Cek apakah user sudah mengerjakan (dari D9: user_quiz_attempts)
- Cek allow_retry jika sudah pernah mengerjakan
- Create attempt baru di D9: Database Quizzes (user_quiz_attempts)
- Set started_at, total_questions
- Query questions dari D9: Database Quizzes (quiz_questions)
- Kirim kuis dan pertanyaan ke Anggota

**5.13 Submit jawaban kuis**
- Menerima jawaban kuis dari Anggota
- Validasi attempt_id dan user_id
- Validasi jawaban (array dengan question_id dan answer)
- Cek time_limit jika ada (validasi waktu pengerjaan)
- Kirim jawaban ke proses 5.14 untuk dihitung

**5.14 Hitung nilai kuis**
- Query questions dan correct_answer dari D9: Database Quizzes (quiz_questions)
- Bandingkan jawaban user dengan correct_answer
- Hitung total points yang didapat
- Hitung total points maksimal
- Hitung persentase nilai: (points_didapat / points_maksimal) * 100
- Tentukan lulus/tidak lulus berdasarkan passing_score
- Kirim hasil perhitungan ke proses 5.15

**5.15 Simpan hasil kuis**
- Update attempt di D9: Database Quizzes (user_quiz_attempts)
- Set answers (JSON), score, percentage, is_passed, completed_at
- Simpan detail jawaban per pertanyaan
- Track waktu pengerjaan (completed_at - started_at)
- Kirim hasil kuis ke Anggota

---

## P 6.0 Dokumen Template

DFD Level 1 untuk proses manajemen dokumen template.

```mermaid
flowchart TB
    %% External Entities
    Admin["👨‍💼 Administrator"]
    Pengurus["👩‍💼 Pengurus"]
    Anggota["👤 Anggota"]
    
    %% Sub-Processes
    P61(("6.1<br/>Input data<br/>template"))
    P62(("6.2<br/>Validasi file<br/>template"))
    P63(("6.3<br/>Upload file<br/>template"))
    P64(("6.4<br/>Simpan data<br/>template"))
    P65(("6.5<br/>Buat versi<br/>template"))
    P66(("6.6<br/>Tampilkan daftar<br/>template"))
    P67(("6.7<br/>Download<br/>template"))
    
    %% Data Stores
    DS6[("D6: Database Templates<br/>(document_templates<br/>document_template_versions<br/>document_template_logs)"]
    FS[("File Storage<br/>(storage/app/templates/)")]
    
    %% Data Flows - Input dari External Entities
    Admin -->|data_template| P61
    Admin -->|info_template| P66
    Admin -->|request_download| P67
    Pengurus -->|data_template| P61
    Pengurus -->|info_template| P66
    Pengurus -->|request_download| P67
    Anggota -->|info_template| P66
    Anggota -->|request_download| P67
    
    %% Data Flows - Antara Sub-Processes
    P61 -->|data_template| P62
    P62 -->|file_valid| P63
    P62 -->|pesan_error| P61
    P63 -->|file_path| P64
    P64 -->|data_template| P65
    P65 -->|data_template| P66
    P66 -->|request_download| P67
    
    %% Data Flows - Data Store
    P64 -->|write_template| DS6
    P65 -->|write_version| DS6
    P67 -->|log_download| DS6
    DS6 -->|template_data| P66
    DS6 -->|template_version_data| P67
    P63 -->|upload_file| FS
    FS -->|download_file| P67
    
    %% Data Flows - Output ke External Entities
    P66 -->|list_template| Admin
    P66 -->|list_template| Pengurus
    P66 -->|list_template| Anggota
    P67 -->|file_template| Admin
    P67 -->|file_template| Pengurus
    P67 -->|file_template| Anggota
    
    %% Styling
    style Admin fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Pengurus fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Anggota fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style P61 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P62 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P63 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P64 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P65 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P66 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P67 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style DS6 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style FS fill:#E8F4F8,stroke:#5C8A2E,stroke-width:2px
```

### Penjelasan Sub-Proses:

**6.1 Input data template**
- Menerima data template dari Administrator atau Pengurus
- Data: name, description, file
- Mengirimkan ke proses 6.2 untuk validasi

**6.2 Validasi file template**
- Validasi name (required, max 255 karakter)
- Validasi file (required, max 10MB)
- Validasi mime type (PDF, DOCX, PPTX, XLSX)
- Jika valid, kirim ke proses 6.3
- Jika tidak valid, kirim error ke proses 6.1

**6.3 Upload file template**
- Generate unique filename
- Simpan file ke File Storage (storage/app/templates/{template_id}/)
- Return file path
- Kirim file path ke proses 6.4

**6.4 Simpan data template**
- Generate slug dari name
- Simpan data template ke D6: Database Templates (document_templates)
- Set created_by dari user yang login
- Set created_at dan updated_at
- Kirim ke proses 6.5 untuk buat versi

**6.5 Buat versi template**
- Buat versi pertama (version_number = 1) atau versi baru
- Simpan data versi ke D6: Database Templates (document_template_versions)
- Update latest_version_id di document_templates
- Set file_path dan file_size
- Kirim ke proses 6.6 untuk ditampilkan

**6.6 Tampilkan daftar template**
- Query semua template dari D6: Database Templates
- Include latest version information
- Include download count (dari document_template_logs)
- Format data untuk ditampilkan
- Kirim list template ke semua external entities

**6.7 Download template**
- Query template dan versi dari D6: Database Templates
- Baca file dari File Storage
- Log download ke D6: Database Templates (document_template_logs)
- Track user yang download dan timestamp
- Kirim file ke user

---

## P 7.0 Kategori

DFD Level 1 untuk proses manajemen kategori (hanya Administrator dan Pengurus).

```mermaid
flowchart TB
    %% External Entities
    Admin["👨‍💼 Administrator"]
    Pengurus["👩‍💼 Pengurus"]
    
    %% Sub-Processes
    P71(("7.1<br/>Input data<br/>kategori"))
    P72(("7.2<br/>Validasi data<br/>kategori"))
    P73(("7.3<br/>Cek duplikasi<br/>kategori"))
    P74(("7.4<br/>Simpan data<br/>kategori"))
    P75(("7.5<br/>Update data<br/>kategori"))
    P76(("7.6<br/>Delete data<br/>kategori"))
    P77(("7.7<br/>Tampilkan daftar<br/>kategori"))
    
    %% Data Store
    DS5[("D5: Database Categories<br/>(categories)"]
    
    %% Data Flows - Input dari External Entities
    Admin -->|data_kategori| P71
    Admin -->|data_update_kategori| P75
    Admin -->|data_delete_kategori| P76
    Admin -->|info_kategori| P77
    Pengurus -->|data_kategori| P71
    Pengurus -->|data_update_kategori| P75
    Pengurus -->|data_delete_kategori| P76
    Pengurus -->|info_kategori| P77
    
    %% Data Flows - Antara Sub-Processes
    P71 -->|data_kategori| P72
    P72 -->|data_valid| P73
    P72 -->|pesan_error| P71
    P73 -->|nama_tersedia| P74
    P73 -->|nama_terpakai| P71
    P74 -->|kategori_baru| P77
    P75 -->|kategori_updated| P77
    P76 -->|kategori_deleted| P77
    
    %% Data Flows - Data Store
    P73 -->|cek_nama| DS5
    P74 -->|write_category| DS5
    P75 -->|update_category| DS5
    P76 -->|delete_category| DS5
    DS5 -->|category_data| P77
    
    %% Data Flows - Output ke External Entities
    P77 -->|list_kategori| Admin
    P77 -->|list_kategori| Pengurus
    
    %% Styling
    style Admin fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Pengurus fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style P71 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P72 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P73 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P74 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P75 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P76 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P77 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style DS5 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
```

### Penjelasan Sub-Proses:

**7.1 Input data kategori**
- Menerima data kategori dari Administrator atau Pengurus
- Data: name, description, slug (auto-generate)
- Mengirimkan ke proses 7.2 untuk validasi

**7.2 Validasi data kategori**
- Validasi name (required, max 255 karakter)
- Validasi description (optional)
- Generate slug dari name jika tidak disediakan
- Jika valid, kirim ke proses 7.3
- Jika tidak valid, kirim error ke proses 7.1

**7.3 Cek duplikasi kategori**
- Query database untuk cek apakah name atau slug sudah terdaftar
- Jika nama tersedia, kirim ke proses 7.4
- Jika nama terpakai, kirim error ke proses 7.1

**7.4 Simpan data kategori**
- Simpan kategori baru ke D5: Database Categories
- Set created_at dan updated_at
- Kirim ke proses 7.7 untuk ditampilkan

**7.5 Update data kategori**
- Validasi data yang akan diupdate
- Update kategori di D5: Database Categories
- Set updated_at
- Kirim ke proses 7.7 untuk ditampilkan

**7.6 Delete data kategori**
- Validasi bahwa kategori tidak digunakan oleh topik
- Hapus kategori dari D5: Database Categories
- Atau set soft delete jika menggunakan soft deletes
- Kirim konfirmasi ke proses 7.7

**7.7 Tampilkan daftar kategori**
- Query semua kategori dari D5: Database Categories
- Include count topik per kategori
- Format data untuk ditampilkan
- Kirim list kategori ke Administrator dan Pengurus

---

## P 8.0 Favorite Saya

DFD Level 1 untuk proses manajemen favorite/bookmark topik.

```mermaid
flowchart TB
    %% External Entities
    Admin["👨‍💼 Administrator"]
    Pengurus["👩‍💼 Pengurus"]
    Anggota["👤 Anggota"]
    
    %% Sub-Processes
    P81(("8.1<br/>Request favorite<br/>saya"))
    P82(("8.2<br/>Query bookmarks<br/>user"))
    P83(("8.3<br/>Toggle bookmark<br/>topik"))
    P84(("8.4<br/>Tampilkan daftar<br/>favorite"))
    
    %% Data Stores
    DS3[("D3: Database Topics<br/>(topics)"]
    DS8[("D8: Database Bookmarks<br/>(bookmarks atau<br/>topics.bookmarks)"]
    
    %% Data Flows - Input dari External Entities
    Admin -->|request_favorite| P81
    Admin -->|toggle_bookmark| P83
    Pengurus -->|request_favorite| P81
    Pengurus -->|toggle_bookmark| P83
    Anggota -->|request_favorite| P81
    Anggota -->|toggle_bookmark| P83
    
    %% Data Flows - Antara Sub-Processes
    P81 -->|request| P82
    P82 -->|bookmark_data| P84
    P83 -->|bookmark_toggled| P82
    
    %% Data Flows - Data Store
    P82 -->|read_bookmarks| DS8
    P83 -->|read_topic| DS3
    P83 -->|write_bookmark| DS8
    P83 -->|delete_bookmark| DS8
    DS8 -->|bookmark_data| P82
    DS3 -->|topic_data| P83
    DS3 -->|favorite_topics| P84
    
    %% Data Flows - Output ke External Entities
    P84 -->|list_favorite| Admin
    P84 -->|list_favorite| Pengurus
    P84 -->|list_favorite| Anggota
    
    %% Styling
    style Admin fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Pengurus fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Anggota fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style P81 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P82 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P83 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P84 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style DS3 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style DS8 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
```

### Penjelasan Sub-Proses:

**8.1 Request favorite saya**
- Menerima request dari semua role untuk melihat favorite mereka
- Validasi session user
- Mengirimkan request ke proses 8.2

**8.2 Query bookmarks user**
- Query bookmarks dari D8: Database Bookmarks berdasarkan user_id
- Atau query dari user.bookmarks array jika menggunakan JSON column
- Include topic data dari D3: Database Topics
- Filter dan sort sesuai request
- Mengirimkan data ke proses 8.4

**8.3 Toggle bookmark topik**
- Menerima request toggle bookmark dari user
- Query topik dari D3: Database Topics untuk validasi
- Cek apakah topik sudah di-bookmark
- Jika sudah, hapus bookmark dari D8: Database Bookmarks
- Jika belum, tambah bookmark ke D8: Database Bookmarks
- Update user.bookmarks array jika menggunakan JSON column
- Mengirimkan update ke proses 8.2

**8.4 Tampilkan daftar favorite**
- Format data favorite topics untuk ditampilkan
- Include topic detail, category, answer count
- Kirim list favorite ke semua external entities

---

## P 9.0 Manajemen Pengguna

DFD Level 1 untuk proses manajemen pengguna (Administrator full access, Pengurus view only).

```mermaid
flowchart TB
    %% External Entities
    Admin["👨‍💼 Administrator"]
    Pengurus["👩‍💼 Pengurus"]
    
    %% Sub-Processes
    P91(("9.1<br/>Input data<br/>pengguna"))
    P92(("9.2<br/>Validasi data<br/>pengguna"))
    P93(("9.3<br/>Cek duplikasi<br/>email"))
    P94(("9.4<br/>Simpan data<br/>pengguna"))
    P95(("9.5<br/>Assign role<br/>pengguna"))
    P96(("9.6<br/>Update data<br/>pengguna"))
    P97(("9.7<br/>Delete data<br/>pengguna"))
    P98(("9.8<br/>Tampilkan daftar<br/>pengguna"))
    
    %% Data Stores
    DS1[("D1: Database Users<br/>(users)"]
    DS2[("D2: Database Roles<br/>(roles<br/>model_has_roles)"]
    
    %% Data Flows - Input dari External Entities
    Admin -->|data_pengguna| P91
    Admin -->|data_update_pengguna| P96
    Admin -->|data_delete_pengguna| P97
    Admin -->|info_pengguna| P98
    Pengurus -->|info_pengguna| P98
    
    %% Data Flows - Antara Sub-Processes
    P91 -->|data_pengguna| P92
    P92 -->|data_valid| P93
    P92 -->|pesan_error| P91
    P93 -->|email_tersedia| P94
    P93 -->|email_terpakai| P91
    P94 -->|data_pengguna| P95
    P95 -->|pengguna_baru| P98
    P96 -->|pengguna_updated| P98
    P97 -->|pengguna_deleted| P98
    
    %% Data Flows - Data Store
    P93 -->|cek_email| DS1
    P94 -->|write_user| DS1
    P95 -->|write_role| DS2
    P96 -->|update_user| DS1
    P97 -->|delete_user| DS1
    DS1 -->|user_data| P98
    DS2 -->|role_data| P95
    DS2 -->|role_data| P98
    
    %% Data Flows - Output ke External Entities
    P98 -->|list_pengguna| Admin
    P98 -->|list_pengguna| Pengurus
    
    %% Styling
    style Admin fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Pengurus fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style P91 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P92 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P93 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P94 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P95 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P96 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P97 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P98 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style DS1 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style DS2 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
```

### Penjelasan Sub-Proses:

**9.1 Input data pengguna**
- Menerima data pengguna dari Administrator (Pengurus tidak bisa create)
- Data: name, email, password, role
- Mengirimkan ke proses 9.2 untuk validasi

**9.2 Validasi data pengguna**
- Validasi name (required, string, max 255)
- Validasi email (required, format email valid)
- Validasi password (required, min 8 karakter)
- Validasi role (harus: administrator, pengurus, atau anggota)
- Jika valid, kirim ke proses 9.3
- Jika tidak valid, kirim error ke proses 9.1

**9.3 Cek duplikasi email**
- Query database untuk cek apakah email sudah terdaftar
- Jika email tersedia, kirim ke proses 9.4
- Jika email terpakai, kirim error ke proses 9.1

**9.4 Simpan data pengguna**
- Hash password menggunakan bcrypt
- Simpan user baru ke D1: Database Users
- Set created_at dan updated_at
- Kirim ke proses 9.5 untuk assign role

**9.5 Assign role pengguna**
- Query role dari D2: Database Roles
- Assign role ke user menggunakan Spatie Permission
- Simpan relasi di model_has_roles (D2: Database Roles)
- Kirim ke proses 9.8 untuk ditampilkan

**9.6 Update data pengguna**
- Menerima request update dari Administrator
- Validasi data yang akan diupdate
- Update user di D1: Database Users
- Jika role berubah, update di D2: Database Roles (model_has_roles)
- Set updated_at
- Kirim ke proses 9.8 untuk ditampilkan

**9.7 Delete data pengguna**
- Menerima request delete dari Administrator
- Hapus user dari D1: Database Users
- Hapus relasi role dari D2: Database Roles (model_has_roles)
- Hapus related data (topik, answers) jika cascade
- Kirim konfirmasi ke proses 9.8

**9.8 Tampilkan daftar pengguna**
- Query semua user dari D1: Database Users
- Query role information dari D2: Database Roles
- Include statistics (jumlah topik, dll)
- Format data untuk ditampilkan
- Kirim list pengguna ke Administrator dan Pengurus (view only)

---

## P 10.0 Manajemen Peran

DFD Level 1 untuk proses manajemen peran/role (hanya Administrator).

```mermaid
flowchart TB
    %% External Entities
    Admin["👨‍💼 Administrator"]
    
    %% Sub-Processes
    P101(("10.1<br/>Input data<br/>peran"))
    P102(("10.2<br/>Validasi data<br/>peran"))
    P103(("10.3<br/>Cek duplikasi<br/>peran"))
    P104(("10.4<br/>Simpan data<br/>peran"))
    P105(("10.5<br/>Assign permission<br/>peran"))
    P106(("10.6<br/>Update data<br/>peran"))
    P107(("10.7<br/>Delete data<br/>peran"))
    P108(("10.8<br/>Tampilkan daftar<br/>peran"))
    
    %% Data Store
    DS2[("D2: Database Roles<br/>(roles<br/>role_has_permissions)"]
    
    %% Data Flows - Input dari External Entities
    Admin -->|data_peran| P101
    Admin -->|data_update_peran| P106
    Admin -->|data_delete_peran| P107
    Admin -->|info_peran| P108
    
    %% Data Flows - Antara Sub-Processes
    P101 -->|data_peran| P102
    P102 -->|data_valid| P103
    P102 -->|pesan_error| P101
    P103 -->|nama_tersedia| P104
    P103 -->|nama_terpakai| P101
    P104 -->|data_peran| P105
    P105 -->|peran_baru| P108
    P106 -->|peran_updated| P108
    P107 -->|peran_deleted| P108
    
    %% Data Flows - Data Store
    P103 -->|cek_nama| DS2
    P104 -->|write_role| DS2
    P105 -->|write_permissions| DS2
    P106 -->|update_role| DS2
    P107 -->|delete_role| DS2
    DS2 -->|role_data| P108
    DS2 -->|permission_data| P105
    
    %% Data Flows - Output ke External Entities
    P108 -->|list_peran| Admin
    
    %% Styling
    style Admin fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style P101 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P102 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P103 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P104 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P105 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P106 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P107 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P108 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style DS2 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
```

### Penjelasan Sub-Proses:

**10.1 Input data peran**
- Menerima data peran dari Administrator
- Data: name, permissions (array)
- Mengirimkan ke proses 10.2 untuk validasi

**10.2 Validasi data peran**
- Validasi name (required, string, max 255, unique)
- Validasi permissions (array, harus ada di list permissions)
- Jika valid, kirim ke proses 10.3
- Jika tidak valid, kirim error ke proses 10.1

**10.3 Cek duplikasi peran**
- Query database untuk cek apakah nama peran sudah terdaftar
- Jika nama tersedia, kirim ke proses 10.4
- Jika nama terpakai, kirim error ke proses 10.1

**10.4 Simpan data peran**
- Simpan role baru ke D2: Database Roles (roles table)
- Set created_at dan updated_at
- Kirim ke proses 10.5 untuk assign permission

**10.5 Assign permission peran**
- Query permissions dari D2: Database Roles
- Assign permissions ke role menggunakan Spatie Permission
- Simpan relasi di role_has_permissions (D2: Database Roles)
- Clear permission cache
- Kirim ke proses 10.8 untuk ditampilkan

**10.6 Update data peran**
- Validasi data yang akan diupdate
- Update role di D2: Database Roles
- Update permissions di role_has_permissions
- Clear permission cache
- Set updated_at
- Kirim ke proses 10.8 untuk ditampilkan

**10.7 Delete data peran**
- Validasi bahwa role tidak digunakan oleh user (cek model_has_roles)
- Hapus role dari D2: Database Roles
- Hapus permissions dari role_has_permissions
- Clear permission cache
- Kirim konfirmasi ke proses 10.8

**10.8 Tampilkan daftar peran**
- Query semua role dari D2: Database Roles
- Include permissions untuk setiap role
- Include user count per role
- Format data untuk ditampilkan
- Kirim list peran ke Administrator

---

## P 11.0 Pengaturan Akun

DFD Level 1 untuk proses pengaturan akun user sendiri.

```mermaid
flowchart TB
    %% External Entities
    Admin["👨‍💼 Administrator"]
    Pengurus["👩‍💼 Pengurus"]
    Anggota["👤 Anggota"]
    
    %% Sub-Processes
    P111(("11.1<br/>Input data<br/>pengaturan"))
    P112(("11.2<br/>Validasi data<br/>pengaturan"))
    P113(("11.3<br/>Cek password<br/>lama"))
    P114(("11.4<br/>Update profil<br/>user"))
    P115(("11.5<br/>Update<br/>password"))
    P116(("11.6<br/>Tampilkan data<br/>profil"))
    
    %% Data Store
    DS1[("D1: Database Users<br/>(users)"]
    
    %% Data Flows - Input dari External Entities
    Admin -->|data_pengaturan| P111
    Admin -->|info_profil| P116
    Pengurus -->|data_pengaturan| P111
    Pengurus -->|info_profil| P116
    Anggota -->|data_pengaturan| P111
    Anggota -->|info_profil| P116
    
    %% Data Flows - Antara Sub-Processes
    P111 -->|data_pengaturan| P112
    P112 -->|data_valid_profil| P114
    P112 -->|data_valid_password| P113
    P112 -->|pesan_error| P111
    P113 -->|password_benar| P115
    P113 -->|password_salah| P111
    P114 -->|profil_updated| P116
    P115 -->|password_updated| P116
    
    %% Data Flows - Data Store
    P113 -->|cek_password| DS1
    P114 -->|update_profil| DS1
    P115 -->|update_password| DS1
    DS1 -->|user_data| P116
    
    %% Data Flows - Output ke External Entities
    P116 -->|data_profil| Admin
    P116 -->|data_profil| Pengurus
    P116 -->|data_profil| Anggota
    
    %% Styling
    style Admin fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Pengurus fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Anggota fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style P111 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P112 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P113 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P114 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P115 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P116 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style DS1 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
```

### Penjelasan Sub-Proses:

**11.1 Input data pengaturan**
- Menerima data pengaturan dari semua role (Administrator, Pengurus, Anggota)
- Data: name, email, password_lama, password_baru, password_baru_confirmation
- Mengirimkan ke proses 11.2 untuk validasi

**11.2 Validasi data pengaturan**
- Validasi name (required, string, max 255)
- Validasi email (required, format email valid, unique kecuali untuk user sendiri)
- Validasi password_baru (optional, min 8 karakter jika diisi)
- Validasi password_baru_confirmation (harus sama dengan password_baru jika password_baru diisi)
- Jika update profil, kirim ke proses 11.4
- Jika update password, kirim ke proses 11.3
- Jika tidak valid, kirim error ke proses 11.1

**11.3 Cek password lama**
- Query user dari D1: Database Users berdasarkan session
- Verifikasi password lama menggunakan hash comparison
- Jika password benar, kirim ke proses 11.5
- Jika password salah, kirim error ke proses 11.1

**11.4 Update profil user**
- Update name dan email di D1: Database Users
- Set updated_at
- Simpan ke database
- Kirim ke proses 11.6 untuk ditampilkan

**11.5 Update password**
- Hash password baru menggunakan bcrypt
- Update password di D1: Database Users
- Set updated_at
- Simpan ke database
- Kirim ke proses 11.6 untuk ditampilkan

**11.6 Tampilkan data profil**
- Query data user dari D1: Database Users berdasarkan session
- Include role information
- Format data untuk ditampilkan
- Kirim data profil ke semua external entities

---

## 📝 Catatan Penting

1. **Format Mermaid.js**: Semua diagram menggunakan format flowchart yang kompatibel dengan Mermaid.js
2. **Data Store**: Menggunakan notasi D1-D9 sesuai dengan DFD Level 0:
   - D1: Database Users
   - D2: Database Roles
   - D3: Database Topics
   - D4: Database Answers
   - D5: Database Categories
   - D6: Database Templates
   - D7: Database Lessons
   - D8: Database Bookmarks
   - D9: Database Quizzes (quizzes, quiz_questions, user_quiz_attempts)
3. **External Entities**: Administrator, Pengurus, dan Anggota sesuai dengan sistem KMS
4. **Sub-Proses**: Setiap sub-proses memiliki fungsi spesifik sesuai dengan alur bisnis
5. **Data Flow**: Semua data flow diberi label yang jelas untuk memudahkan pemahaman
6. **Permission-based Access**: Setiap proses melakukan validasi role dan permission sebelum memberikan akses

---

## 🎯 Cara Menggunakan

1. **Mermaid Live Editor**: Copy kode diagram ke https://mermaid.live/
2. **GitHub/GitLab**: Paste ke file markdown di repository
3. **VS Code**: Install extension "Markdown Preview Mermaid Support"
4. **Dokumentasi Online**: Docusaurus, GitBook, dll yang support Mermaid

---

## 📊 Tabel Ringkasan Akses Per Role

| Proses | Administrator | Pengurus | Anggota |
|--------|--------------|----------|---------|
| 1.0 Login | ✅ | ✅ | ✅ |
| 2.0 Dashboard | ✅ | ✅ | ✅ |
| 3.0 Forum Diskusi | ✅ (Full CRUD) | ✅ (Full CRUD) | ✅ (View, Create, Edit Own) |
| 4.0 Topik Saya | ✅ | ✅ | ✅ |
| 5.0 Pembelajaran | ✅ (Full CRUD) | ✅ (Full CRUD) | ✅ (View, Download) |
| 6.0 Dokumen Template | ✅ (Full CRUD) | ✅ (Full CRUD) | ✅ (View, Download) |
| 7.0 Kategori | ✅ (Full CRUD) | ✅ (Full CRUD) | ❌ |
| 8.0 Favorite Saya | ✅ | ✅ | ✅ |
| 9.0 Manajemen Pengguna | ✅ (Full CRUD) | ✅ (View Only) | ❌ |
| 10.0 Manajemen Peran | ✅ (Full CRUD) | ❌ | ❌ |
| 11.0 Pengaturan Akun | ✅ | ✅ | ✅ |

---

**Dibuat untuk:** Knowledge Management System (KMS)  
**Format:** Mermaid.js Flowchart  
**Versi:** 1.0  
**Tanggal:** 2024


