@extends('program.layout')

@section('content')
    @include('landing.partials.nav')

    @php
        $trackMap = [
            'mentoring-al-quran' => 'Pembinaan Spiritual',
            'pelatihan-pementor' => 'Kepemimpinan',
            'kajian-keislaman' => 'Literasi Islam',
            'humas-kampanye-digital' => 'Kreatif Digital',
            'penggalangan-sosial' => 'Aksi Sosial',
            'kewirausahaan-kemitraan-strategis' => 'Kewirausahaan',
        ];

        $programs = collect(config('programs'))->map(function ($program) use ($trackMap) {
            $program['track'] = $trackMap[$program['slug']] ?? 'Program Unggulan';

            return $program;
        })->values();

        $heroStats = [
            ['label' => 'Sesi mentoring/semester', 'value' => '480+', 'hint' => 'Halaqah onsite & daring terjadwal'],
            ['label' => 'Pementor aktif', 'value' => '160', 'hint' => 'Lintas 12 fakultas siap mendampingi'],
            ['label' => 'Kolaborasi prodi', 'value' => '45', 'hint' => 'Agenda sinergi BEM, UKM, dan fakultas'],
        ];

        $pillars = [
            ['eyebrow' => 'Experience Studio', 'title' => 'Ruang belajar imersif', 'text' => 'Kombinasi halaqah kecil, coaching pribadi, dan sesi tematik dengan perangkat visual yang konsisten.'],
            ['eyebrow' => 'Mentor Forge', 'title' => 'Pembinaan fasilitator', 'text' => 'Kurva tuntas: mindset, instruksional desain, microteaching, hingga monitoring KPI mentoring.'],
            ['eyebrow' => 'Impact Network', 'title' => 'Aksi sosial & kampanye', 'text' => 'Pendekatan kampanye kreatif, sosial entrepreneurship, dan respon cepat bencana berbasis data.'],
        ];

        $journey = [
            ['phase' => 'Discover', 'time' => 'Jan-Feb', 'description' => 'Kurasi mentee & mentor dengan asesmen spiritual dan portofolio kepemimpinan.'],
            ['phase' => 'Design', 'time' => 'Mar-Apr', 'description' => 'Modul tematik, kalender aksi, dan training intensif diselaraskan antar divisi.'],
            ['phase' => 'Deliver', 'time' => 'Mei-Jul', 'description' => 'Implementasi mentoring, coaching mingguan, dan konten kampanye digital.'],
            ['phase' => 'Showcase', 'time' => 'Agu-Sep', 'description' => 'Demo akhir, pelaporan dampak, dan aktivasi kemitraan lanjutan.'],
        ];

        $quotes = [
            ['quote' => 'Portal baru ini bikin kami mudah memetakan jalur pembinaan tanpa harus buka banyak dokumen.', 'name' => 'Najla Rahmania', 'role' => 'Lead Facilitator'],
            ['quote' => 'Layout modernnya membantu saya menjelaskan value chain BOM-PAI kepada sponsor kampus.', 'name' => 'Dr. Rahmat Hidayat', 'role' => 'Pembina Akademik'],
            ['quote' => 'Sebagai creative lead, saya tinggal mengambil CTA dan highlight tiap program ketika menyiapkan konten digital.', 'name' => 'Alya Prameswari', 'role' => 'Creative Lead Humas'],
        ];

        $tracks = $programs->groupBy('track')->keys();

        $focusHighlights = [
            ['emoji' => '🌱', 'title' => 'Studio Kurasi', 'body' => 'Panel kurikulum yang memetakan target ruhiyah, leadership, hingga social impact per batch.', 'pill' => 'Realtime board'],
            ['emoji' => '🧭', 'title' => 'Compass Timeline', 'body' => 'Roadmap otomatis memadukan jadwal halaqah, showcase, dan kampanye humas.', 'pill' => 'Sinkronisasi'],
            ['emoji' => '🤝', 'title' => 'Kolaborasi Mitra', 'body' => 'Toolkit pitch deck & CTA siap bagi tim sponsorship saat presentasi ke fakultas/korporasi.', 'pill' => 'Partner ready'],
        ];
    @endphp

    <main class="relative z-10">
        <section class="mx-auto max-w-6xl px-6 pt-24 pb-12">
            <div class="grid gap-10 lg:grid-cols-[1.15fr,0.85fr]">
                <div class="relative rounded-[40px] border border-white/40 bg-white/95 p-10 shadow-[0_60px_120px_-70px_rgba(28,167,167,0.55)]">
                    <div class="absolute inset-y-0 right-6 hidden w-px bg-gradient-to-b from-transparent via-brand-teal/40 to-transparent lg:block"></div>
                    <p class="text-xs font-semibold uppercase tracking-[0.6em] text-brand-teal">Portal Program BOM-PAI</p>
                    <h1 class="mt-6 font-heading text-4xl text-brand-ink lg:text-5xl">Temukan jalur mentoring yang selaras dengan karakter kampusmu.</h1>
                    <p class="mt-5 text-base leading-relaxed text-slate-600">Versi terbaru laman program ini mengusung komposisi editorial dengan layer kaca, detail tipografi humanis, dan blok konten modular agar tim lebih mudah mengkurasi pengalaman pembinaan.</p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="#program-grid" class="inline-flex items-center justify-center rounded-full bg-brand-teal px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-teal/40 transition hover:brightness-90">Buka katalog</a>
                        <a href="https://wa.me/6281234567890" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-full border border-brand-teal/40 px-6 py-3 text-sm font-semibold text-brand-ink hover:border-brand-ink">Atur diskusi kurikulum</a>
                    </div>
                    <div class="mt-10 grid gap-6 md:grid-cols-3">
                        @foreach($heroStats as $stat)
                            <div class="rounded-[26px] border border-brand-sky/45 bg-brand-mist p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-brand-teal">{{ $stat['label'] }}</p>
                                <p class="mt-3 font-heading text-3xl text-brand-ink">{{ $stat['value'] }}</p>
                                <p class="text-xs text-slate-500">{{ $stat['hint'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute -inset-6 rounded-[46px] bg-gradient-to-br from-brand-sky/25 via-transparent to-transparent blur-2xl"></div>
                    <div class="relative flex h-full flex-col gap-5 rounded-[40px] border border-white/30 bg-gradient-to-br from-white/90 to-white/70 p-6 shadow-[0_45px_100px_-65px_rgba(28,167,167,0.6)]">
                        @foreach($focusHighlights as $highlight)
                            <div class="rounded-[28px] border border-white/40 bg-white/85 p-5 shadow-[0_30px_60px_-50px_rgba(6,20,35,0.7)]">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="text-3xl">{{ $highlight['emoji'] }}</span>
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-[0.4em] text-brand-teal">{{ $highlight['title'] }}</p>
                                            <p class="text-[11px] font-semibold text-slate-400">{{ $highlight['pill'] }}</p>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-3 text-sm text-slate-600">{{ $highlight['body'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-6 pb-12">
            <div class="rounded-[40px] border border-white/40 bg-white/95 p-10 shadow-[0_45px_90px_-65px_rgba(28,167,167,0.55)]">
                <div class="grid gap-8 lg:grid-cols-3">
                    @foreach($pillars as $pillar)
                        <div class="rounded-[30px] border border-brand-sky/45 bg-brand-mist p-7">
                            <p class="text-xs font-semibold uppercase tracking-[0.5em] text-brand-teal">{{ $pillar['eyebrow'] }}</p>
                            <h3 class="mt-3 font-heading text-2xl text-brand-ink">{{ $pillar['title'] }}</h3>
                            <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $pillar['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="program-grid" class="mx-auto max-w-6xl px-6 pb-16">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.55em] text-brand-teal">Katalog Kurasi</p>
                    <h2 class="mt-2 font-heading text-3xl text-brand-ink">Setiap divisi punya warna, ritme, dan output berbeda.</h2>
                    <p class="mt-3 text-sm text-slate-600">Gulirkan kartu untuk melihat logistik, highlight modul, hingga CTA pendaftaran masing-masing program.</p>
                </div>
                <div class="flex flex-wrap gap-2 rounded-full border border-brand-sky/40 bg-white/80 px-4 py-2 text-xs font-semibold text-brand-ink shadow-[0_20px_60px_-45px_rgba(28,167,167,0.4)]">
                    @foreach($tracks as $track)
                        <span class="rounded-full bg-brand-sky/20 px-3 py-1">{{ $track }}</span>
                    @endforeach
                </div>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-[280px,1fr]">
                <div class="space-y-4 rounded-[30px] border border-white/40 bg-white/90 p-6 shadow-[0_30px_60px_-50px_rgba(28,167,167,0.5)]">
                    <h3 class="text-sm font-semibold text-brand-ink">Sorotan Operasional</h3>
                    <ul class="space-y-3 text-sm text-slate-600">
                        <li class="flex items-center gap-2"><span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-sky/25 text-brand-teal">◎</span>Penyelarasan sprint mentoring, humas, dan aksi sosial dalam satu board.</li>
                        <li class="flex items-center gap-2"><span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-sky/25 text-brand-teal">◎</span>Template log mentoring dengan indikator ruhiyah & leadership terukur.</li>
                        <li class="flex items-center gap-2"><span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-sky/25 text-brand-teal">◎</span>Pendampingan creative lab untuk setiap showcase dan campaign digital.</li>
                        <li class="flex items-center gap-2"><span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-sky/25 text-brand-teal">◎</span>Pairing mentor lintas fakultas berdasarkan minat dan kompetensi.</li>
                    </ul>
                </div>
                <div class="grid gap-6 md:grid-cols-2">
                    @foreach($programs as $program)
                        <div class="group flex h-full flex-col rounded-[34px] border border-white/40 bg-white/90 p-7 shadow-[0_35px_70px_-60px_rgba(28,167,167,0.5)]">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br {{ $program['gradient'] ?? 'from-teal-500/20 to-cyan-200/40' }} text-3xl">
                                        {{ $program['emoji'] ?? '✨' }}
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-semibold uppercase tracking-[0.45em] text-brand-teal">{{ $program['track'] }}</p>
                                        <h3 class="font-heading text-2xl text-brand-ink">{{ $program['title'] }}</h3>
                                    </div>
                                </div>
                                <a href="{{ $program['cta']['register_url'] ?? '#' }}" target="_blank" rel="noopener" class="rounded-full border border-brand-teal/40 px-3 py-1 text-xs font-semibold text-brand-teal">Daftar</a>
                            </div>
                            <p class="mt-4 flex-1 text-sm text-slate-600">{{ $program['description'] }}</p>
                            <div class="mt-5 flex flex-wrap gap-2">
                                @foreach(collect($program['outcomes'] ?? [])->take(2) as $outcome)
                                    <span class="rounded-full bg-brand-sky/15 px-3 py-1 text-xs text-brand-ink">{{ \Illuminate\Support\Str::limit($outcome, 50) }}</span>
                                @endforeach
                            </div>
                            <div class="mt-5 grid gap-3 text-xs text-slate-500">
                                @foreach(collect($program['logistics'] ?? [])->take(3) as $logistic)
                                    <div class="flex items-center justify-between rounded-2xl bg-brand-mist px-3 py-2">
                                        <span class="font-semibold text-brand-ink">{{ $logistic['label'] }}</span>
                                        <span>{{ $logistic['value'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-6 flex items-center justify-between text-sm font-semibold">
                                <a href="{{ route('program.show', $program['slug']) }}" class="inline-flex items-center text-brand-teal transition group-hover:text-brand-ink">Profil lengkap →</a>
                                <span class="text-slate-400">{{ count($program['modules'] ?? []) }} modul</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-6 pb-16">
            <div class="grid gap-10 rounded-[40px] border border-white/40 bg-white/95 p-10 shadow-[0_45px_90px_-60px_rgba(6,20,35,0.6)] lg:grid-cols-[0.9fr,1.1fr]">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.55em] text-brand-teal">Roadmap Semester</p>
                    <h2 class="mt-3 font-heading text-3xl text-brand-ink">Ritme teratur dari onboarding hingga showcase.</h2>
                    <p class="mt-4 text-sm text-slate-600">Desain baru ini memecah perjalanan menjadi empat fase agar tim mudah menyinkronkan mentoring, konten, dan aksi sosial secara paralel.</p>
                </div>
                <div class="space-y-5">
                    @foreach($journey as $step)
                        <div class="flex gap-6 rounded-[28px] border border-brand-sky/45 bg-brand-mist p-6">
                            <div class="w-20 text-xs font-semibold uppercase tracking-[0.35em] text-brand-teal">{{ $step['phase'] }}<span class="block text-[11px] text-slate-400">{{ $step['time'] }}</span></div>
                            <p class="text-sm text-slate-600">{{ $step['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-6 pb-16">
            <div class="rounded-[40px] border border-white/40 bg-white/95 p-10 shadow-[0_45px_90px_-55px_rgba(28,167,167,0.5)]">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.55em] text-brand-teal">Suara Kolaborator</p>
                        <h2 class="mt-2 font-heading text-3xl text-brand-ink">Testimoni dari mentee, mentor, hingga creative lead.</h2>
                    </div>
                    <div class="rounded-full border border-brand-teal/35 px-4 py-2 text-xs font-semibold text-brand-ink">Dikurasikan 2023-2025</div>
                </div>
                <div class="mt-8 grid gap-6 md:grid-cols-3">
                    @foreach($quotes as $quote)
                        <div class="rounded-[30px] border border-brand-sky/45 bg-brand-mist p-6">
                            <p class="text-sm leading-relaxed text-slate-600">“{{ $quote['quote'] }}”</p>
                            <div class="mt-4">
                                <p class="font-heading text-base text-brand-ink">{{ $quote['name'] }}</p>
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-brand-teal">{{ $quote['role'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-6 pb-24">
            <div class="flex flex-col gap-6 rounded-[44px] border border-brand-teal/35 bg-gradient-to-br from-brand-ink via-brand-ink/90 to-brand-teal/78 p-12 text-brand-ink shadow-[0_60px_110px_-72px_rgba(15,23,42,0.55)] lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.55em] text-brand-sky/80">Siap merancang musim mentoring baru?</p>
                    <h2 class="mt-4 font-heading text-3xl text-white">Kami bantu mix & match program sesuai karakter kampusmu.</h2>
                    <p class="mt-4 text-sm text-white/80">Tim desain kurikulum BOM-PAI siap mendampingi dari sesi konsultasi, pilot program, hingga perjanjian kemitraan resmi.</p>
                </div>
                <div class="flex flex-col gap-4 sm:flex-row">
                    <a href="https://forms.gle/bommentoring" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-full bg-white/95 px-6 py-3 text-sm font-semibold text-brand-ink shadow-[0_12px_26px_-18px_rgba(255,255,255,0.55)]">Ajukan presentasi</a>
                    <a href="https://wa.me/6281234567890" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-full border border-white/70 px-6 py-3 text-sm font-semibold text-white">Hubungi sekretariat</a>
                </div>
            </div>
        </section>
    </main>

    @include('landing.partials.footer')
@endsection