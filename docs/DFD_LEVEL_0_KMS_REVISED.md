```mermaid
flowchart TB
    %% ==============================================================================
    %% EXTERNAL ENTITIES
    %% ==============================================================================
    Admin["👨‍💼 Administrator"]
    Pengurus["👩‍💼 Pengurus"]
    Anggota["👤 Anggota"]

    %% ==============================================================================
    %% PROCESSES (P1.0 - P11.0)
    %% ==============================================================================
    P1(("P 1.0<br/>Login"))
    P2(("P 2.0<br/>Dashboard"))
    P3(("P 3.0<br/>Forum Diskusi"))
    P4(("P 4.0<br/>Topik Saya"))
    P5(("P 5.0<br/>Pembelajaran"))
    P6(("P 6.0<br/>Dokumen Template"))
    P7(("P 7.0<br/>Kategori"))
    P8(("P 8.0<br/>Favorit Saya"))
    P9(("P 9.0<br/>Manajemen Pengguna"))
    P10(("P 10.0<br/>Manajemen Peran"))
    P11(("P 11.0<br/>Pengaturan Akun"))

    %% ==============================================================================
    %% DATA STORES (Sesuai DFD Level 1)
    %% ==============================================================================
    DS1[("D1: Users")]
    DS2[("D2: Roles")]
    DS3[("D3: Topics")]
    DS4[("D4: Answers")]
    DS5[("D5: Categories")]
    DS6[("D6: Templates")]
    DS7[("D7: Lessons")]
    DS8[("D8: Bookmarks")]
    DS9[("D9: Quizzes")]
    FS[("File Storage")]

    %% ==============================================================================
    %% FLOWS: P 1.0 LOGIN
    %% ==============================================================================
    %% Admin: All (Input & Output)
    Admin -->|data_login| P1
    P1 -->|info_login| Admin

    %% Pengurus: All (Input & Output)
    Pengurus -->|data_login| P1
    P1 -->|info_login| Pengurus

    %% Anggota: All (Input & Output)
    Anggota -->|data_login| P1
    P1 -->|info_login| Anggota

    P1 <--> DS1
    P1 <--> DS2

    %% ==============================================================================
    %% FLOWS: P 2.0 DASHBOARD
    %% ==============================================================================
    %% Admin: All
    Admin -->|data_dasbor| P2
    P2 -->|info_dasbor| Admin

    %% Pengurus: All
    Pengurus -->|data_dasbor| P2
    P2 -->|info_dasbor| Pengurus

    %% Anggota: Exclude Input 'data_dasbor', Include Output 'info_dasbor'
    P2 -->|info_dasbor| Anggota

    DS1 & DS3 & DS6 --> P2

    %% ==============================================================================
    %% FLOWS: P 3.0 FORUM DISKUSI
    %% ==============================================================================
    %% Admin: All
    Admin -->|data_forum_diskusi| P3
    P3 -->|info_forum_diskusi| Admin

    %% Pengurus: All
    Pengurus -->|data_forum_diskusi| P3
    P3 -->|info_forum_diskusi| Pengurus

    %% Anggota: All (Participation)
    Anggota -->|data_forum_diskusi| P3
    P3 -->|info_forum_diskusi| Anggota

    P3 <--> DS3
    P3 <--> DS4
    DS5 --> P3

    %% ==============================================================================
    %% FLOWS: P 4.0 TOPIK SAYA
    %% ==============================================================================
    %% Admin: All
    Admin -->|data_topik_saya| P4
    P4 -->|info_topik_saya| Admin

    %% Pengurus: All
    Pengurus -->|data_topik_saya| P4
    P4 -->|info_topik_saya| Pengurus

    %% Anggota: All
    Anggota -->|data_topik_saya| P4
    P4 -->|info_topik_saya| Anggota

    P4 <--> DS3

    %% ==============================================================================
    %% FLOWS: P 5.0 PEMBELAJARAN
    %% ==============================================================================
    %% Admin: All
    Admin -->|data_pembelajaran| P5
    P5 -->|info_pembelajaran| Admin

    %% Pengurus: All
    Pengurus -->|data_pembelajaran| P5
    P5 -->|info_pembelajaran| Pengurus

    %% Anggota: Include Input 'data_pembelajaran' (Not in exclusion list) & Output
    Anggota -->|data_pembelajaran| P5
    P5 -->|info_pembelajaran| Anggota

    P5 <--> DS7
    P5 <--> DS9
    P5 <--> FS

    %% ==============================================================================
    %% FLOWS: P 6.0 DOKUMEN TEMPLATE
    %% ==============================================================================
    %% Admin: All
    Admin -->|data_dokumen_template| P6
    P6 -->|info_dokumen_template| Admin

    %% Pengurus: All
    Pengurus -->|data_dokumen_template| P6
    P6 -->|info_dokumen_template| Pengurus

    %% Anggota: Exclude Input 'data_dokumen_template', Include Output
    P6 -->|info_dokumen_template| Anggota

    P6 <--> DS6
    P6 <--> FS

    %% ==============================================================================
    %% FLOWS: P 7.0 KATEGORI
    %% ==============================================================================
    %% Admin: All
    Admin -->|data_kategori| P7
    P7 -->|info_kategori| Admin

    %% Pengurus: All
    Pengurus -->|data_kategori| P7
    P7 -->|info_kategori| Pengurus

    %% Anggota: Exclude Input 'data_kategori', Include Output (Implicit view)
    P7 -->|info_kategori| Anggota

    P7 <--> DS5

    %% ==============================================================================
    %% FLOWS: P 8.0 FAVORIT SAYA
    %% ==============================================================================
    %% "kecuali 8.0 favorite saya tidak memiliki input"
    %% Admin: Output Only
    P8 -->|info_favorit_saya| Admin

    %% Pengurus: Output Only
    P8 -->|info_favorit_saya| Pengurus

    %% Anggota: Output Only (Exclude Input 'data_favorite_saya')
    P8 -->|info_favorit_saya| Anggota

    P8 <--> DS8
    DS3 --> P8

    %% ==============================================================================
    %% FLOWS: P 9.0 MANAJEMEN PENGGUNA
    %% ==============================================================================
    %% Admin: All
    Admin -->|data_manajemen_pengguna| P9
    P9 -->|info_manajemen_pengguna| Admin

    %% Pengurus: Exclude Input 'data_manajemen_pengguna', Include Output 'info_manajemen_pengguna'
    P9 -->|info_manajemen_pengguna| Pengurus

    %% Anggota: Exclude Input & Output (No Access)
    
    P9 <--> DS1
    P9 <--> DS2

    %% ==============================================================================
    %% FLOWS: P 10.0 MANAJEMEN PERAN
    %% ==============================================================================
    %% Admin: All
    Admin -->|data_manajemen_peran| P10
    P10 -->|info_manajemen_peran| Admin

    %% Pengurus: Exclude Input & Output
    
    %% Anggota: Exclude Input & Output

    P10 <--> DS2

    %% ==============================================================================
    %% FLOWS: P 11.0 PENGATURAN AKUN
    %% ==============================================================================
    %% Admin: All
    Admin -->|data_pengaturan_akun| P11
    P11 -->|info_pengaturan_akun| Admin

    %% Pengurus: All
    Pengurus -->|data_pengaturan_akun| P11
    P11 -->|info_pengaturan_akun| Pengurus

    %% Anggota: All
    Anggota -->|data_pengaturan_akun| P11
    P11 -->|info_pengaturan_akun| Anggota

    P11 <--> DS1

    %% ==============================================================================
    %% STYLING
    %% ==============================================================================
    style Admin fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Pengurus fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    style Anggota fill:#E8F4F8,stroke:#2E86AB,stroke-width:2px
    
    style P1 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P2 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P3 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P4 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P5 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P6 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P7 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P8 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P9 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P10 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px
    style P11 fill:#FFF5E5,stroke:#CC6600,stroke-width:2px

    style DS1 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style DS2 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style DS3 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style DS4 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style DS5 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style DS6 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style DS7 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style DS8 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style DS9 fill:#D5E8D4,stroke:#82B366,stroke-width:2px
    style FS fill:#F8F8F8,stroke:#666666,stroke-width:2px,stroke-dasharray: 5 5
```
