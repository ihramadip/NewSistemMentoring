@php
$stats = [
    ['label' => 'Mentee aktif', 'value' => '3.500+'],
    ['label' => 'Pementor tersertifikasi', 'value' => '120'],
    ['label' => 'Program tahunan', 'value' => '24'],
    ['label' => 'Kolaborasi kampus & komunitas', 'value' => '18'],
];

$programs = [
    ['title' => "Mentoring Al-Qur'an", 'emoji' => '📖', 'description' => 'Pendampingan halaqah tematik untuk menguatkan akidah, ibadah, dan adab mahasiswa.', 'link' => '#portal', 'gradient' => 'from-brand-sky/70 via-brand-mist to-brand-teal/80'],
    ['title' => 'Pelatihan Pementor', 'emoji' => '🎓', 'description' => 'Bootcamp kompetensi mentoring dan microteaching bersama dosen & trainer nasional.', 'link' => '#blog', 'gradient' => 'from-brand-gold/40 via-brand-sky/45 to-brand-teal/80'],
    ['title' => 'Kajian Keislaman', 'emoji' => '🕌', 'description' => 'Kuliah umum dengan narasumber inspiratif yang mengaitkan Islam dan isu kampus.', 'link' => '#blog', 'gradient' => 'from-brand-teal/80 via-brand-mist to-brand-gold/45'],
    ['title' => 'Humas & Kampanye Digital', 'emoji' => '📣', 'description' => 'Konten edukasi lintas platform untuk menggaungkan nilai Islami yang relevan.', 'link' => '#dokumentasi', 'gradient' => 'from-brand-sky/65 via-brand-teal/75 to-brand-mist'],
    ['title' => 'Penggalangan Sosial', 'emoji' => '🤝', 'description' => 'Gerakan kepedulian (TFM, beasiswa, respon bencana) yang transparan dan akuntabel.', 'link' => '#dokumentasi', 'gradient' => 'from-brand-gold/45 via-brand-mist to-brand-sky/65'],
    ['title' => 'Kewirausahaan & Sponsor', 'emoji' => '💡', 'description' => 'Unit kreatif untuk produk muslim-friendly dan kemitraan strategis.', 'link' => '#blog', 'gradient' => 'from-brand-teal/75 via-brand-mist to-brand-gold/40'],
];

$programDetails = [
    'mentoring-quran' => [
        'activities' => [
            ['judul' => 'Tilawah Harian', 'deskripsi' => 'Membaca dan memahami Al-Qur\'an secara rutin dengan pendampingan mentor', 'frekuensi' => 'Harian', 'durasi' => '30 menit'],
            ['judul' => 'Tadabbur Mingguan', 'deskripsi' => 'Merefleksikan makna Al-Qur\'an dalam kehidupan sehari-hari bersama mentor', 'frekuensi' => 'Mingguan', 'durasi' => '90 menit'],
            ['judul' => 'Kajian Tafsir', 'deskripsi' => 'Mengkaji penjelasan ayat-ayat Al-Qur\'an dengan pendekatan kontekstual', 'frekuensi' => 'Mingguan', 'durasi' => '120 menit'],
        ]
    ],

    'pelatihan-pementor' => [
        'activities' => [
            ['judul' => 'Training of Trainers', 'deskripsi' => 'Pelatihan teknik mentoring dan microteaching', 'frekuensi' => 'Bulanan', 'durasi' => '16 jam'],
            ['judul' => 'Modul Pengembangan Diri', 'deskripsi' => 'Peningkatan soft skills dan karakter pementor', 'frekuensi' => 'Bulanan', 'durasi' => '8 jam'],
        ]
    ],

    'kajian-keislaman' => [
        'activities' => [
            ['judul' => 'Kuliah Umum', 'deskripsi' => 'Pemaparan topik-topik keislaman dengan narasumber inspiratif', 'frekuensi' => 'Mingguan', 'durasi' => '90 menit'],
            ['judul' => 'Diskusi Kelompok', 'deskripsi' => 'Interaksi intens antara peserta untuk menggali pemahaman isu kontemporer', 'frekuensi' => 'Mingguan', 'durasi' => '120 menit'],
        ]
    ],

    'humas-kampanye-digital' => [
        'activities' => [
            ['judul' => 'Content Creation', 'deskripsi' => 'Membuat konten edukasi Islami untuk berbagai platform digital', 'frekuensi' => 'Mingguan', 'durasi' => '4 jam'],
            ['judul' => 'Campaign Strategy', 'deskripsi' => 'Merancang kampanye digital untuk menyebarkan nilai-nilai Islami', 'frekuensi' => 'Bulanan', 'durasi' => '6 jam'],
        ]
    ],

    'penggalangan-sosial' => [
        'activities' => [
            ['judul' => 'TFM (Tetap Fokus Mengaji)', 'deskripsi' => 'Kegiatan kemahasiswaan yang menggabungkan spiritualitas dan kepedulian sosial', 'frekuensi' => 'Bulanan', 'durasi' => '24 jam'],
            ['judul' => 'Bakti Sosial', 'deskripsi' => 'Kegiatan pelayanan kepada masyarakat membutuhkan', 'frekuensi' => 'Bulanan', 'durasi' => '8 jam'],
        ]
    ],

    'kewirausahaan-sponsor' => [
        'activities' => [
            ['judul' => 'Product Development', 'deskripsi' => 'Mengembangkan produk-produk muslim-friendly yang inovatif', 'frekuensi' => 'Bulanan', 'durasi' => '12 jam'],
            ['judul' => 'Partnership Building', 'deskripsi' => 'Membangun kemitraan strategis dengan berbagai pihak', 'frekuensi' => 'Kuartal', 'durasi' => '8 jam'],
        ]
    ]
];


$documentation = [
    ['title' => 'Wisuda Mentoring 2024', 'month' => 'Mei 2024', 'type' => 'Video Highlight', 'description' => 'Rangkaian apresiasi 400+ mentee dengan branded stage dan storytelling.', 'image' => 'https://images.unsplash.com/photo-1488190211105-8b0e65b80b4e?auto=format&fit=crop&w=1000&q=80'],
    ['title' => 'Kelas Intensif Pementor', 'month' => 'Juli 2024', 'type' => 'Foto', 'description' => 'Sesi praktik kurikulum GBMO dengan modul interaktif.', 'image' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1000&q=80'],
    ['title' => 'Safari Kajian Ramadhan', 'month' => 'Maret 2025', 'type' => 'Gallery', 'description' => 'Kolaborasi masjid kampus untuk kajian tematik lintas fakultas.', 'image' => 'images/kajian.png'],
    ['title' => 'Pelatihan Digital Marketing', 'month' => 'April 2024', 'type' => 'Foto', 'description' => 'Workshop strategi digital untuk promosi kegiatan BOM-PAI.', 'image' => 'https://images.unsplash.com/photo-1542744095-291d1f67b221?auto=format&fit=crop&w=1000&q=80'],
    ['title' => 'Seminar Kepemimpinan', 'month' => 'Juni 2024', 'type' => 'Video', 'description' => 'Diskusi bersama tokoh muda inspiratif dan alumni BOM-PAI.', 'image' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1000&q=80'],
    ['title' => 'Kegiatan Sosial Ramadhan', 'month' => 'April 2025', 'type' => 'Gallery', 'description' => 'Bakti sosial dan buka bersama dhuafa bersama mentee dan pementor.', 'image' => 'https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?auto=format&fit=crop&w=1000&q=80'],
];

$blogPosts = [
    ['category' => 'Pengumuman', 'title' => 'Open Recruitment Pementor 2025', 'excerpt' => 'Raih sertifikasi mentoring resmi BOM-PAI dan akses modul eksklusif.', 'date' => '15 Nov 2025', 'link' => route('mentor.register.create')],
    ['category' => 'Artikel', 'title' => 'Metode Tadabbur untuk Gen-Z Kampus', 'excerpt' => 'Pendekatan storytelling dan micro habit untuk kelas mentoring.', 'date' => '07 Nov 2025'],
    ['category' => 'Liputan', 'title' => 'Kolaborasi Mentoring x FISIP', 'excerpt' => 'Serial diskusi lintas program studi untuk isu sosial kontemporer.', 'date' => '28 Okt 2025'],
    ['category' => 'Artikel', 'title' => 'Pentingnya Mentoring dalam Pendidikan Islam', 'excerpt' => 'Peran mentor dalam membentuk karakter dan akhlak mahasiswa.', 'date' => '20 Okt 2025'],
    ['category' => 'Pengumuman', 'title' => 'Jadwal Mentoring Semester Ganjil 2025', 'excerpt' => 'Informasi lengkap mengenai jadwal kegiatan mentoring.', 'date' => '15 Okt 2025'],
    ['category' => 'Liputan', 'title' => 'Refleksi Kegiatan Tadarus Bersama', 'excerpt' => 'Catatan haru dari kegiatan rutin membaca Al-Qur\'an.', 'date' => '10 Okt 2025'],
];

$testimonials = [
    ['name' => 'Najla Rizqi', 'role' => 'Alumni Pementor 2022', 'quote' => 'BOM-PAI memberi ruang mengeksplor pedagogi Islami yang modern namun tetap syar\'i. Jaringan alumninya solid untuk karier pasca kampus.'],
    ['name' => 'Dr. H. Rahmat, Lc., MA', 'role' => 'Pembina Akademik', 'quote' => 'Transformasi digital mentoring membuat monitoring jauh lebih rapi. Portal BOM-PAI jadi best practice di UNISBA.'],
    ['name' => 'Rizky Pramana', 'role' => 'Mentee 2024', 'quote' => 'Materi mentoring terasa relevan dengan kehidupan mahasiswa. Saya merasa lebih percaya diri memimpin komunitas kecil di fakultas.'],
    ['name' => 'Siti Aisyah', 'role' => 'Mentee 2023', 'quote' => 'Mentoring membantu saya memahami pentingnya adab dalam ilmu. Kini saya lebih percaya diri berdakwah di komunitas.'],
    ['name' => 'Ahmad Fauzan', 'role' => 'Alumni Pementor 2021', 'quote' => 'Pengalaman menjadi pementor memberi saya keterampilan kepemimpinan yang sangat berharga di dunia kerja.'],
    ['name' => 'Laila Nur', 'role' => 'Mentee 2025', 'quote' => 'Program mentoring memberi saya fondasi spiritual yang kuat di tengah dinamika kampus yang keras.'],
];

$contacts = [
    ['label' => 'Email Sekretariat', 'value' => 'bom-pai@unisba.ac.id', 'link' => 'mailto:bom-pai@unisba.ac.id'],
    ['label' => 'Instagram', 'value' => '@bom.pai.unisba', 'link' => 'https://instagram.com'],
    ['label' => 'TikTok', 'value' => '@bompaiofficial', 'link' => 'https://tiktok.com'],
    ['label' => 'YouTube', 'value' => 'BOM-PAI Channel', 'link' => 'https://youtube.com'],
    ['label' => 'WA Center', 'value' => '+62 812-3456-7890', 'link' => 'https://wa.me/6281234567890'],
];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>BOM-PAI UNISBA — Membina Kepribadian Islami</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-body bg-brand-mist text-brand-ink">
        <div class="relative overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-[620px] bg-gradient-to-br from-brand-ink via-brand-ink/90 to-brand-teal/70 opacity-95"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(153,238,255,0.22),_transparent_62%)]"></div>

            @include('landing.partials.nav')

            <main class="relative z-10">
                @include('landing.partials.hero')
                @include('landing.partials.about')
                @include('landing.partials.programs')
                @include('landing.partials.documentation')
                @include('landing.partials.blog')
                @include('landing.partials.portal')
                @include('landing.partials.alumni')
                @include('landing.partials.contact')
            </main>

            @include('landing.partials.footer')
        </div>
    </body>
</html>
