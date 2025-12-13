<?php

return [
    'mentoring-al-quran' => [
        'slug' => 'mentoring-al-quran',
        'title' => "Mentoring Al-Qur'an",
        'emoji' => '📖',
        'tagline' => 'Pendampingan halaqah tematik yang menumbuhkan kedekatan spiritual dan adab mahasiswa.',
        'description' => 'Mentoring Al-Qur\'an dirancang untuk membantu mentee memahami kandungan ayat secara kontekstual, menguatkan hafalan, serta menumbuhkan kebiasaan ibadah yang konsisten melalui halaqah kecil yang hangat.',
        'hero_image' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1200&q=80',
        'gradient' => 'from-teal-500/20 to-cyan-200/40',
        'logistics' => [
            ['label' => 'Durasi', 'value' => '12 pekan (1 semester)'],
            ['label' => 'Jadwal', 'value' => "Setiap malam Jum'at"],
            ['label' => 'Lokasi', 'value' => 'Masjid kampus & kelas daring'],
            ['label' => 'Peserta', 'value' => 'Mahasiswa semester 1-6'],
        ],
        'highlights' => [
            ['label' => 'Halaqah aktif', 'value' => '25 kelompok'],
            ['label' => 'Rasio mentor:mentee', 'value' => '1:10'],
            ['label' => 'Modul tematik', 'value' => '12 bab inspiratif'],
        ],
        'outcomes' => [
            'Memperbaiki bacaan dan tadabbur Al-Qur\'an secara konsisten.',
            'Menginternalisasi akhlak kampus berbasis nilai keislaman.',
            'Menghasilkan jurnal refleksi pekanan sebagai bahan coaching.',
        ],
        'modules' => [
            ['title' => 'Tahsin & Makharij', 'duration' => 'Pekan 1-3', 'description' => 'Kelas penyegaran bacaan dengan feedback individual dan rekaman suara.'],
            ['title' => 'Tadabbur Tematik', 'duration' => 'Pekan 4-7', 'description' => 'Mengaitkan ayat dengan isu literasi digital, relasi sosial, dan kepemimpinan mahasiswa.'],
            ['title' => 'Adab Penuntut Ilmu', 'duration' => 'Pekan 8-10', 'description' => 'Menghidupkan akhlak keseharian melalui studi kasus kehidupan kampus.'],
            ['title' => 'Proyek Dakwah Mini', 'duration' => 'Pekan 11-12', 'description' => 'Kelompok membuat konten dakwah kreatif sebagai output evaluasi akhir.'],
        ],
        'initiatives' => [
            ['name' => 'Halaqah Tematik', 'summary' => 'Diskusi pekanan 10-12 mentee dengan fokus tadabbur dan coaching akhlak.', 'cadence' => 'Mingguan', 'owner' => 'Koordinator Halaqah'],
            ['name' => 'Majelis Tahsin Intensif', 'summary' => 'Kelas kecil perbaikan bacaan dengan rekaman audio dan review mentor.', 'cadence' => 'Dwimingguan', 'owner' => 'Lead Facilitator'],
            ['name' => 'Studio Dakwah Kreatif', 'summary' => 'Produksi konten refleksi mentee sebagai laporan perkembangan spiritual.', 'cadence' => 'Project Based', 'owner' => 'Learning Coach'],
        ],
        'mentors' => [
            ['name' => 'Ust. Ahmad Fauzi', 'role' => 'Koordinator Halaqah', 'bio' => 'Alumni LIPIA dengan fokus metodologi tadabbur interaktif.', 'photo' => 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=crop&w=300&q=80'],
            ['name' => 'Najla Rahmania', 'role' => 'Lead Facilitator', 'bio' => 'Pementor senior dengan sertifikasi tahsin bersanad.', 'photo' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=300&q=80'],
            ['name' => 'Rizky Pratama', 'role' => 'Learning Coach', 'bio' => 'Mengawal journaling mentee dan evaluasi personal.', 'photo' => 'https://images.unsplash.com/photo-1504593811423-6dd665756598?auto=format&fit=crop&w=300&q=80'],
        ],
        'resources' => [
            ['title' => 'Modul Tadabbur Gen-Z', 'type' => 'PDF', 'description' => '12 bab pembahasan ayat tematik.', 'link' => '#'],
            ['title' => 'Playlist Murattal', 'type' => 'Audio', 'description' => 'Rujukan bacaan untuk latihan mandiri.', 'link' => '#'],
        ],
        'cta' => [
            'register_url' => 'https://forms.gle/bommentoring',
            'contact_label' => 'WhatsApp Sekretariat',
            'contact_url' => 'https://wa.me/6281234567890',
        ],
    ],

    'pelatihan-pementor' => [
        'slug' => 'pelatihan-pementor',
        'title' => 'Pelatihan Pementor',
        'emoji' => '🎓',
        'tagline' => 'Bootcamp kompetensi pedagogi Islami dan microteaching untuk calon pementor kampus.',
        'description' => 'Program ini memadukan kurikulum teaching skill, spiritual leadership, dan pengalaman lapangan agar peserta siap memimpin halaqah maupun project mentoring.',
        'hero_image' => 'https://images.unsplash.com/photo-1523580846011-d3a5bc25702b?auto=format&fit=crop&w=1200&q=80',
        'gradient' => 'from-blue-500/15 to-sky-200/40',
        'logistics' => [
            ['label' => 'Durasi', 'value' => '8 pekan intensif'],
            ['label' => 'Jadwal', 'value' => 'Sabtu-Minggu (09.00-15.00)'],
            ['label' => 'Lokasi', 'value' => 'Training Center UNISBA'],
            ['label' => 'Peserta', 'value' => 'Mahasiswa minimal semester 3'],
        ],
        'highlights' => [
            ['label' => 'Sesi praktik', 'value' => '20 jam microlesson'],
            ['label' => 'Mentor tamu', 'value' => '8 praktisi nasional'],
            ['label' => 'Lisensi', 'value' => 'Sertifikat BOM-PAI'],
        ],
        'outcomes' => [
            'Menguasai silabus mentoring dan teknik fasilitasi dialog.',
            'Mampu merancang asesmen mentee serta refleksi terstruktur.',
            'Mendapatkan coaching individual sebelum diterjunkan ke lapangan.',
        ],
        'modules' => [
            ['title' => 'Mindset & Akhlak Pementor', 'duration' => 'Pekan 1', 'description' => 'Meneguhkan niat, visi, dan etika kepemimpinan ruhiyah.'],
            ['title' => 'Instructional Design', 'duration' => 'Pekan 2-3', 'description' => 'Mempelajari kurikulum GBMO dan teknik penyusunan sesi.'],
            ['title' => 'Microteaching & Feedback', 'duration' => 'Pekan 4-5', 'description' => 'Simulasi kelas kecil dengan penilaian rubrik.'],
            ['title' => 'Field Project', 'duration' => 'Pekan 6-8', 'description' => 'Peserta memimpin mentoring nyata dengan supervisor.'],
        ],
        'initiatives' => [
            ['name' => 'Bootcamp Kompetensi', 'summary' => 'Sesi intensif bersama dosen dan trainer nasional dengan simulasi real-time.', 'cadence' => 'Akhir Pekan', 'owner' => 'Lead Trainer'],
            ['name' => 'Mentor Shadowing', 'summary' => 'Peserta mendampingi mentor senior untuk observasi dan journaling lesson plan.', 'cadence' => 'Rolling', 'owner' => 'Microteaching Coach'],
            ['name' => 'Coaching Clinic', 'summary' => 'Sesi konsultasi 1:1 terkait kesiapan menjadi pementor lapangan.', 'cadence' => 'Mingguan', 'owner' => 'Assessment Specialist'],
        ],
        'mentors' => [
            ['name' => 'Dr. Siti Rahmawati', 'role' => 'Lead Trainer', 'bio' => 'Dosen PAI dan konsultan kurikulum nasional.', 'photo' => 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=crop&w=300&q=80'],
            ['name' => 'Hilmi Yustika', 'role' => 'Microteaching Coach', 'bio' => 'Fasilitator berpengalaman di berbagai LDK kampus.', 'photo' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=300&q=80'],
            ['name' => 'Faris Maulana', 'role' => 'Assessment Specialist', 'bio' => 'Mendesain rubrik kompetensi pementor.', 'photo' => 'https://images.unsplash.com/photo-1527980965255-d3b416303d12?auto=format&fit=crop&w=300&q=80'],
        ],
        'resources' => [
            ['title' => 'Toolkit Microteaching', 'type' => 'Slide', 'description' => 'Template sesi mentoring siap pakai.', 'link' => '#'],
            ['title' => 'Checklist Evaluasi', 'type' => 'PDF', 'description' => 'Instrumen monitoring pementor baru.', 'link' => '#'],
        ],
        'cta' => [
            'register_url' => 'https://forms.gle/pelatihanpementor',
            'contact_label' => 'Admin Pelatihan',
            'contact_url' => 'https://wa.me/6281234567890',
        ],
    ],

    'kajian-keislaman' => [
        'slug' => 'kajian-keislaman',
        'title' => 'Kajian Keislaman',
        'emoji' => '🕌',
        'tagline' => 'Serial kuliah umum dengan narasumber inspiratif yang mengontekstualkan Islam dan isu kampus.',
        'description' => 'Kajian Keislaman menghadirkan tokoh lintas disiplin untuk membahas tema spiritual, sosial, hingga teknologi, sehingga mahasiswa memiliki sudut pandang Islami yang relevan.',
        'hero_image' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1200&q=80',
        'gradient' => 'from-emerald-500/20 to-lime-200/40',
        'logistics' => [
            ['label' => 'Durasi', 'value' => '10 pekan'],
            ['label' => 'Jadwal', 'value' => 'Setiap Rabu, 16.00 WIB'],
            ['label' => 'Lokasi', 'value' => 'Auditorium utama & live streaming'],
            ['label' => 'Peserta', 'value' => 'Umum, prioritas mahasiswa'],
        ],
        'highlights' => [
            ['label' => 'Narasumber', 'value' => '10 tokoh inspiratif'],
            ['label' => 'Kolaborasi', 'value' => '6 fakultas'],
            ['label' => 'Publikasi', 'value' => '15 konten recap'],
        ],
        'outcomes' => [
            'Memahami kerangka berpikir Islam terkait isu kontemporer.',
            'Memperkuat kemampuan literasi dan dialog lintas disiplin.',
            'Menghasilkan rangkuman kajian sebagai bahan mentoring.',
        ],
        'modules' => [
            ['title' => 'Spirit Tauhid di Era Digital', 'duration' => 'Sesi 1-2', 'description' => 'Membedah adab bermedia dan literasi algoritma.'],
            ['title' => 'Keadilan Sosial', 'duration' => 'Sesi 3-5', 'description' => 'Kajian fiqh sosial dan civic engagement mahasiswa.'],
            ['title' => 'Leadership & Integrity', 'duration' => 'Sesi 6-8', 'description' => 'Mengelola organisasi kampus sesuai prinsip syariah.'],
            ['title' => 'Forum Diskusi Terbuka', 'duration' => 'Sesi 9-10', 'description' => 'Peserta mempresentasikan insight dan rencana aksi.'],
        ],
        'initiatives' => [
            ['name' => 'Kuliah Tematik', 'summary' => 'Sesi utama dengan narasumber nasional dan sesi tanya jawab interaktif.', 'cadence' => 'Mingguan', 'owner' => 'Kurator Kajian'],
            ['name' => 'Reading Circle', 'summary' => 'Kelompok kecil yang merangkum literatur dan menulis insight untuk mentee.', 'cadence' => 'Dwimingguan', 'owner' => 'Moderator'],
            ['name' => 'Campaign Recap', 'summary' => 'Tim konten mengemas highlight kajian ke format video & carousel.', 'cadence' => 'Setiap Sesi', 'owner' => 'Content Producer'],
        ],
        'mentors' => [
            ['name' => 'Dr. Rahmat Hidayat', 'role' => 'Kurator Kajian', 'bio' => 'Pakar pemikiran Islam kontemporer.', 'photo' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=300&q=80'],
            ['name' => 'Siti Aminah', 'role' => 'Moderator', 'bio' => 'MC dan fasilitator dialog lintas fakultas.', 'photo' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=300&q=80'],
            ['name' => 'Bima Kurnia', 'role' => 'Content Producer', 'bio' => 'Mengemas ulang insight menjadi konten edukatif.', 'photo' => 'https://images.unsplash.com/photo-1527980965255-d3b416303d12?auto=format&fit=crop&w=300&q=80'],
        ],
        'resources' => [
            ['title' => 'Ringkasan Kajian', 'type' => 'Notion', 'description' => 'Catatan kolaboratif setiap sesi.', 'link' => '#'],
            ['title' => 'Playlist Rekaman', 'type' => 'Video', 'description' => 'Streaming ulang di YouTube BOM-PAI.', 'link' => '#'],
        ],
        'cta' => [
            'register_url' => 'https://forms.gle/kajiankeislaman',
            'contact_label' => 'Tim Kajian',
            'contact_url' => 'https://wa.me/6281234567890',
        ],
    ],

    'humas-kampanye-digital' => [
        'slug' => 'humas-kampanye-digital',
        'title' => 'Humas & Kampanye Digital',
        'emoji' => '📣',
        'tagline' => 'Unit kreatif yang menggaungkan nilai Islami di platform digital melalui kampanye terkurasi.',
        'description' => 'Tim humas memproduksi konten multiplatform, mengelola community engagement, dan memastikan pesan dakwah tersampaikan dengan bahasa visual yang relevan dengan mahasiswa.',
        'hero_image' => 'https://images.unsplash.com/photo-1529333166437-7750a6dd5a70?auto=format&fit=crop&w=1200&q=80',
        'gradient' => 'from-purple-500/15 to-fuchsia-200/40',
        'logistics' => [
            ['label' => 'Durasi', 'value' => '14 pekan (rolling)'],
            ['label' => 'Jadwal', 'value' => 'Briefing Selasa & Kamis'],
            ['label' => 'Lokasi', 'value' => 'Studio kreatif & remote'],
            ['label' => 'Peserta', 'value' => 'Divisi konten & desain'],
        ],
        'highlights' => [
            ['label' => 'Konten/bulan', 'value' => '60+ publikasi'],
            ['label' => 'Channel', 'value' => 'IG, TikTok, YouTube'],
            ['label' => 'Growth rate', 'value' => '+18% followers'],
        ],
        'outcomes' => [
            'Menguasai creative brief Islami dan storytelling visual.',
            'Meningkatkan skill produksi foto, video, dan copywriting.',
            'Membangun ritme kampanye lintas program mentoring.',
        ],
        'modules' => [
            ['title' => 'Brand Narrative', 'duration' => 'Pekan 1-2', 'description' => 'Mendefinisikan tone of voice dan sistem identitas.'],
            ['title' => 'Content Sprint', 'duration' => 'Pekan 3-6', 'description' => 'Implementasi kalender konten mingguan.'],
            ['title' => 'Production Lab', 'duration' => 'Pekan 7-10', 'description' => 'Pemantapan skill foto, video, motion, dan caption.'],
            ['title' => 'Campaign Showcase', 'duration' => 'Pekan 11-14', 'description' => 'Peluncuran kampanye kolaborasi antar divisi.'],
        ],
        'initiatives' => [
            ['name' => 'Content Studio', 'summary' => 'Produksi rutin untuk IG, TikTok, YouTube dengan standar storyboard.', 'cadence' => 'Harian', 'owner' => 'Creative Lead'],
            ['name' => 'Community Care', 'summary' => 'Tim interaksi yang merespon DM, komentar, dan membuat FAQ Islami.', 'cadence' => 'Setiap Hari', 'owner' => 'Community Manager'],
            ['name' => 'Amplify Labs', 'summary' => 'Eksperimen format baru (live session, kolaborasi influencer kampus).', 'cadence' => 'Bulanan', 'owner' => 'Video Strategist'],
        ],
        'mentors' => [
            ['name' => 'Alya Prameswari', 'role' => 'Creative Lead', 'bio' => 'Art director dengan pengalaman agency Islami.', 'photo' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=300&q=80'],
            ['name' => 'Rafi Hidayat', 'role' => 'Video Strategist', 'bio' => 'Spesialis short-form dakwah content.', 'photo' => 'https://images.unsplash.com/photo-1527980965255-d3b416303d12?auto=format&fit=crop&w=300&q=80'],
            ['name' => 'Nisrina Lazuardi', 'role' => 'Community Manager', 'bio' => 'Mengelola interaksi dan respon DM 24/7.', 'photo' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=300&q=80'],
        ],
        'resources' => [
            ['title' => 'Template Konten', 'type' => 'Figma', 'description' => 'Komponen UI untuk IG & TikTok.', 'link' => '#'],
            ['title' => 'Playbook Crisis', 'type' => 'Doc', 'description' => 'Standar respon komentar dan DM.', 'link' => '#'],
        ],
        'cta' => [
            'register_url' => 'https://forms.gle/humaskampanye',
            'contact_label' => 'Lead Humas',
            'contact_url' => 'https://wa.me/6281234567890',
        ],
    ],

    'penggalangan-sosial' => [
        'slug' => 'penggalangan-sosial',
        'title' => 'Penggalangan Sosial',
        'emoji' => '🤝',
        'tagline' => 'Gerakan kepedulian kolektif untuk respon bencana, pendidikan, dan program kemanusiaan kampus.',
        'description' => 'Divisi ini menginisiasi kampanye kebaikan, koordinasi relawan, serta pelaporan transparan agar mahasiswa dapat berkontribusi nyata kepada masyarakat.',
        'hero_image' => 'https://images.unsplash.com/photo-1509099836639-18ba1795216d?auto=format&fit=crop&w=1200&q=80',
        'gradient' => 'from-orange-500/20 to-amber-200/40',
        'logistics' => [
            ['label' => 'Durasi', 'value' => 'Program tahunan (rolling)'],
            ['label' => 'Jadwal', 'value' => 'Briefing bulanan'],
            ['label' => 'Lokasi', 'value' => 'Lapangan aksi & posko kampus'],
            ['label' => 'Peserta', 'value' => 'Relawan lintas fakultas'],
        ],
        'highlights' => [
            ['label' => 'Dana tersalurkan', 'value' => 'Rp 480 jt / tahun'],
            ['label' => 'Relawan aktif', 'value' => '320 mahasiswa'],
            ['label' => 'Program sosial', 'value' => '18 agenda'],
        ],
        'outcomes' => [
            'Meningkatkan empati dan kepemimpinan lapangan.',
            'Menerapkan manajemen program dan pelaporan donasi.',
            'Membangun jejaring dengan lembaga sosial terpercaya.',
        ],
        'modules' => [
            ['title' => 'Humanitarian Mindset', 'duration' => 'Pekan 1', 'description' => 'Menguatkan niat dan adab berdakwah sosial.'],
            ['title' => 'Design for Impact', 'duration' => 'Pekan 2-4', 'description' => 'Menyusun proposal, target, dan indikator keberhasilan.'],
            ['title' => 'Field Execution', 'duration' => 'Pekan 5-10', 'description' => 'Pelaksanaan aksi lapangan + manajemen relawan.'],
            ['title' => 'Reporting & Storytelling', 'duration' => 'Pekan 11-12', 'description' => 'Publikasi transparansi dan testimoni penerima manfaat.'],
        ],
        'initiatives' => [
            ['name' => 'Fundraising Squad', 'summary' => 'Merancang kampanye donasi digital dan booth on-site.', 'cadence' => 'Bulanan', 'owner' => 'Finance Lead'],
            ['name' => 'Rapid Response Team', 'summary' => 'Relawan yang siap turun saat ada bencana atau isu kemanusiaan.', 'cadence' => 'On Demand', 'owner' => 'Relief Coordinator'],
            ['name' => 'Impact Lab', 'summary' => 'Menyusun laporan transparansi dan story penerima manfaat.', 'cadence' => 'Setiap Program', 'owner' => 'Pembina Sosial'],
        ],
        'mentors' => [
            ['name' => 'Ust. Salman Yusuf', 'role' => 'Pembina Sosial', 'bio' => 'Aktivis kemanusiaan dan konsultan zakat.', 'photo' => 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=crop&w=300&q=80'],
            ['name' => 'Nabila Choirunnisa', 'role' => 'Relief Coordinator', 'bio' => 'Berpengalaman memimpin respon cepat bencana.', 'photo' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=300&q=80'],
            ['name' => 'Andi Prakoso', 'role' => 'Finance Lead', 'bio' => 'Mengawal transparansi dan audit internal.', 'photo' => 'https://images.unsplash.com/photo-1527980965255-d3b416303d12?auto=format&fit=crop&w=300&q=80'],
        ],
        'resources' => [
            ['title' => 'Template Proposal Aksi', 'type' => 'Doc', 'description' => 'Format standar kegiatan sosial.', 'link' => '#'],
            ['title' => 'Dashboard Transparansi', 'type' => 'Sheet', 'description' => 'Contoh pelaporan dana real-time.', 'link' => '#'],
        ],
        'cta' => [
            'register_url' => 'https://forms.gle/penggalangansosial',
            'contact_label' => 'PIC Sosial',
            'contact_url' => 'https://wa.me/6281234567890',
        ],
    ],

    'kewirausahaan-kemitraan-strategis' => [
        'slug' => 'kewirausahaan-kemitraan-strategis',
        'title' => 'Kewirausahaan & Kemitraan Strategis',
        'emoji' => '💡',
        'tagline' => 'Mengembangkan unit bisnis muslim-friendly dan jejaring sponsor untuk keberlanjutan program mentoring.',
        'description' => 'Divisi ini melatih mahasiswa dalam mengemas produk dakwah, negosiasi sponsorship, serta pengelolaan brand partnership yang profesional.',
        'hero_image' => 'https://images.unsplash.com/photo-1485217988980-11786ced9454?auto=format&fit=crop&w=1200&q=80',
        'gradient' => 'from-indigo-500/20 to-blue-200/40',
        'logistics' => [
            ['label' => 'Durasi', 'value' => 'Program berkelanjutan'],
            ['label' => 'Jadwal', 'value' => 'Sprint mingguan'],
            ['label' => 'Lokasi', 'value' => 'Co-working BOM-PAI'],
            ['label' => 'Peserta', 'value' => 'Tim bisnis & hubungan eksternal'],
        ],
        'highlights' => [
            ['label' => 'Brand partner', 'value' => '15 mitra aktif'],
            ['label' => 'Produk kreatif', 'value' => '9 lini merchandise'],
            ['label' => 'Nilai kolaborasi', 'value' => 'Rp 350 jt / tahun'],
        ],
        'outcomes' => [
            'Memahami strategi monetisasi dakwah modern.',
            'Mahir menyusun deck penawaran dan pitch singkat.',
            'Mengelola event kolaborasi dengan standar profesional.',
        ],
        'modules' => [
            ['title' => 'Business Model Dakwah', 'duration' => 'Pekan 1-2', 'description' => 'Menyusun proposisi nilai dan segmentasi pasar.'],
            ['title' => 'Product Lab', 'duration' => 'Pekan 3-5', 'description' => 'Produksi merchandise & layanan mentoring corporate.'],
            ['title' => 'Sponsorship Funnel', 'duration' => 'Pekan 6-8', 'description' => 'Negosiasi, legalitas, dan aktivasi hak mitra.'],
            ['title' => 'Impact Review', 'duration' => 'Pekan 9-10', 'description' => 'Laporan dashboard finansial dan social impact.'],
        ],
        'initiatives' => [
            ['name' => 'Product Garage', 'summary' => 'Tim riset dan produksi merchandise muslim-friendly.', 'cadence' => 'Mingguan', 'owner' => 'Business Advisor'],
            ['name' => 'Partnership Desk', 'summary' => 'Negosiasi sponsorship, penyusunan MoU, dan aktivasi hak mitra.', 'cadence' => 'Rolling', 'owner' => 'Partnership Lead'],
            ['name' => 'Corporate Mentoring', 'summary' => 'Paket kolaborasi untuk perusahaan/instansi yang ingin adopsi kurikulum mentoring.', 'cadence' => 'Project Based', 'owner' => 'Finance Mentor'],
        ],
        'mentors' => [
            ['name' => 'Yusuf Rahadian', 'role' => 'Business Advisor', 'bio' => 'Founder startup syariah dan konsultan sponsorship.', 'photo' => 'https://images.unsplash.com/photo-1504593811423-6dd665756598?auto=format&fit=crop&w=300&q=80'],
            ['name' => 'Maya Hapsari', 'role' => 'Partnership Lead', 'bio' => 'Berpengalaman menjalin kemitraan kampus-industri.', 'photo' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=300&q=80'],
            ['name' => 'Bagas Pramudito', 'role' => 'Finance Mentor', 'bio' => 'Mengawal proyeksi keuangan dan compliance syariah.', 'photo' => 'https://images.unsplash.com/photo-1527980965255-d3b416303d12?auto=format&fit=crop&w=300&q=80'],
        ],
        'resources' => [
            ['title' => 'Deck Sponsorship', 'type' => 'Pitch', 'description' => 'Template presentasi kolaborasi.', 'link' => '#'],
            ['title' => 'Dashboard Penjualan', 'type' => 'Sheet', 'description' => 'Contoh pelacakan revenue & stok.', 'link' => '#'],
        ],
        'cta' => [
            'register_url' => 'https://forms.gle/kewirausahaanbom',
            'contact_label' => 'Tim Kemitraan',
            'contact_url' => 'https://wa.me/6281234567890',
        ],
    ],
];
