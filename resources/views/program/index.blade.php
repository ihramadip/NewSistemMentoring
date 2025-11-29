@extends('program.layout')

@section('content')
    @include('landing.partials.nav')

    <main class="relative z-10">
        <div class="mx-auto max-w-6xl px-6 py-24">
            <div class="flex flex-col items-center text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.6em] text-[#1CA7A7]">Program Unggulan</p>
                <h2 class="section-title font-heading text-4xl text-[#0a2b4e] mb-6">Setiap program memberikan pengalaman unik.</h2>
                <p class="max-w-2xl text-white">
                    Program-program kami dirancang secara komprehensif untuk membentuk pribadi muslim yang kompeten, berakhlak mulia, dan siap menghadapi tantangan zaman.
                </p>
            </div>

            <!-- Mentoring Al-Qur'an -->
            <div class="mt-20 rounded-3xl border border-white bg-white p-8 shadow-[0_25px_45px_-35px_rgba(10,43,78,0.35)]">
                <div class="flex flex-col md:flex-row gap-8 items-center">
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-teal-500/20 to-cyan-200/40 text-4xl">
                        📖
                    </div>
                    <div class="md:w-2/3">
                        <h3 class="font-heading text-2xl text-[#0a2b4e] mb-4">Mentoring Al-Qur'an</h3>
                        <p class="text-slate-600 mb-4">
                            Pendampingan halaqah tematik yang dirancang khusus untuk menguatkan akidah, ibadah, dan adab mahasiswa.
                            Program ini tidak hanya fokus pada tilawah Al-Qur'an, namun juga memahamkan nilai-nilai kehidupan yang terkandung di dalamnya.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="rounded-full bg-[#E4F4F4] px-4 py-2 inline-block">
                                <span class="text-xs font-semibold text-[#1CA7A7]">Durasi Program: 12 pekan (3 bulan)</span>
                            </div>
                            <div class="rounded-full bg-[#E4F4F4] px-4 py-2 inline-block">
                                <span class="text-xs font-semibold text-[#1CA7A7]">Jadwal Kegiatan: Setiap malam Jum'at</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pelatihan Pementor -->
            <div class="mt-12 rounded-3xl border border-white bg-white p-8 shadow-[0_25px_45px_-35px_rgba(10,43,78,0.35)]">
                <div class="flex flex-col md:flex-row gap-8 items-center">
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500/15 to-sky-200/40 text-4xl">
                        🎓
                    </div>
                    <div class="md:w-2/3">
                        <h3 class="font-heading text-2xl text-[#0a2b4e] mb-4">Pelatihan Pementor</h3>
                        <p class="text-slate-600 mb-4">
                            Bootcamp kompetensi mentoring dan microteaching yang bekerja sama dengan dosen dan trainer nasional.
                            Program ini menyiapkan kader-kader muda yang siap menjadi mentor bagi adik kelasnya, dengan pendekatan pedagogi Islami kontemporer.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="rounded-full bg-[#E4F4F4] px-4 py-2 inline-block">
                                <span class="text-xs font-semibold text-[#1CA7A7]">Durasi Program: 8 pekan (2 bulan)</span>
                            </div>
                            <div class="rounded-full bg-[#E4F4F4] px-4 py-2 inline-block">
                                <span class="text-xs font-semibold text-[#1CA7A7]">Jadwal Kegiatan: Sabtu-Minggu (Weekend)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kajian Keislaman -->
            <div class="mt-12 rounded-3xl border border-white bg-white p-8 shadow-[0_25px_45px_-35px_rgba(10,43,78,0.35)]">
                <div class="flex flex-col md:flex-row gap-8 items-center">
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500/20 to-lime-200/40 text-4xl">
                        🕌
                    </div>
                    <div class="md:w-2/3">
                        <h3 class="font-heading text-2xl text-[#0a2b4e] mb-4">Kajian Keislaman</h3>
                        <p class="text-slate-600 mb-4">
                            Kuliah umum dengan narasumber inspiratif yang mengaitkan ajaran Islam dengan isu-isu aktual di kampus.
                            Program ini membantu mahasiswa memahami relevansi ajaran Islam dalam berbagai aspek kehidupan kontemporer.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="rounded-full bg-[#E4F4F4] px-4 py-2 inline-block">
                                <span class="text-xs font-semibold text-[#1CA7A7]">Durasi Program: 10 pekan (seminggu sekali)</span>
                            </div>
                            <div class="rounded-full bg-[#E4F4F4] px-4 py-2 inline-block">
                                <span class="text-xs font-semibold text-[#1CA7A7]">Jadwal Kegiatan: Setiap Rabu</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Humas & Kampanye Digital -->
            <div class="mt-12 rounded-3xl border border-white bg-white p-8 shadow-[0_25px_45px_-35px_rgba(10,43,78,0.35)]">
                <div class="flex flex-col md:flex-row gap-8 items-center">
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-500/15 to-fuchsia-200/40 text-4xl">
                        📣
                    </div>
                    <div class="md:w-2/3">
                        <h3 class="font-heading text-2xl text-[#0a2b4e] mb-4">Humas & Kampanye Digital</h3>
                        <p class="text-slate-600 mb-4">
                            Unit produksi konten edukasi lintas platform yang menggaungkan nilai-nilai Islami secara relevan dengan generasi muda.
                            Program ini melatih mahasiswa dalam berkomunikasi Islam secara efektif di era digital.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="rounded-full bg-[#E4F4F4] px-4 py-2 inline-block">
                                <span class="text-xs font-semibold text-[#1CA7A7]">Durasi Program: 14 pekan (sepanjang semester)</span>
                            </div>
                            <div class="rounded-full bg-[#E4F4F4] px-4 py-2 inline-block">
                                <span class="text-xs font-semibold text-[#1CA7A7]">Jadwal Kegiatan: Setiap Selasa</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Penggalangan Sosial -->
            <div class="mt-12 rounded-3xl border border-white bg-white p-8 shadow-[0_25px_45px_-35px_rgba(10,43,78,0.35)]">
                <div class="flex flex-col md:flex-row gap-8 items-center">
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-500/20 to-amber-200/40 text-4xl">
                        🤝
                    </div>
                    <div class="md:w-2/3">
                        <h3 class="font-heading text-2xl text-[#0a2b4e] mb-4">Penggalangan Sosial</h3>
                        <p class="text-slate-600 mb-4">
                            Gerakan kepedulian mahasiswa yang melibatkan kegiatan TFM (Tetap Fokus Mengaji), beasiswa,
                            dan respon bencana secara transparan dan akuntabel. Program ini menumbuhkan rasa empati dan tanggung jawab sosial.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="rounded-full bg-[#E4F4F4] px-4 py-2 inline-block">
                                <span class="text-xs font-semibold text-[#1CA7A7]">Durasi Program: Kegiatan terjadwal (bulanan)</span>
                            </div>
                            <div class="rounded-full bg-[#E4F4F4] px-4 py-2 inline-block">
                                <span class="text-xs font-semibold text-[#1CA7A7]">Jadwal Kegiatan: Bulanan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kewirausahaan & Sponsor -->
            <div class="mt-12 rounded-3xl border border-white bg-white p-8 shadow-[0_25px_45px_-35px_rgba(10,43,78,0.35)]">
                <div class="flex flex-col md:flex-row gap-8 items-center">
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500/20 to-blue-200/40 text-4xl">
                        💡
                    </div>
                    <div class="md:w-2/3">
                        <h3 class="font-heading text-2xl text-[#0a2b4e] mb-4">Kewirausahaan & Kemitraan Strategis</h3>
                        <p class="text-slate-600 mb-4">
                            Unit kreatif yang mengembangkan produk-produk muslim-friendly dan membangun kemitraan strategis.
                            Program ini melatih mahasiswa dalam berwirausaha dengan pendekatan nilai-nilai Islami.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="rounded-full bg-[#E4F4F4] px-4 py-2 inline-block">
                                <span class="text-xs font-semibold text-[#1CA7A7]">Durasi Program: Kegiatan berkelanjutan</span>
                            </div>
                            <div class="rounded-full bg-[#E4F4F4] px-4 py-2 inline-block">
                                <span class="text-xs font-semibold text-[#1CA7A7]">Jadwal Kegiatan: Setiap pekan (aktif)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('landing.partials.footer')
@endsection