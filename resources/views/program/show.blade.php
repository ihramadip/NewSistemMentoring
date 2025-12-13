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

        $initiatives = collect($program['initiatives'] ?? [
            ['name' => 'Inisiatif Utama', 'summary' => $program['description'] ?? '', 'cadence' => 'Mingguan', 'owner' => 'Koordinator Program'],
        ]);
        $logistics = collect($program['logistics'] ?? []);
        $highlights = collect($program['highlights'] ?? []);
        $modules = collect($program['modules'] ?? []);
        $outcomes = collect($program['outcomes'] ?? []);
        $resources = collect($program['resources'] ?? []);

        $heroStats = $logistics->take(3);
        if ($heroStats->count() < 3) {
            $heroStats = $heroStats->merge($highlights->take(3 - $heroStats->count()));
        }
        if ($heroStats->isEmpty()) {
            $heroStats = collect([
                ['label' => 'Durasi', 'value' => '12 pekan'],
                ['label' => 'Jadwal', 'value' => 'Pekan berjalan'],
                ['label' => 'Lokasi', 'value' => 'Hybrid'],
            ]);
        }

        $moduleTimeline = $modules->map(fn ($module, $index) => array_merge($module, ['index' => $index + 1]));
        $initiativeStory = $initiatives->map(fn ($item) => [
            'title' => $item['name'],
            'meta' => $item['cadence'] ?? 'Rolling',
            'owner' => $item['owner'] ?? 'Lead Mentor',
            'body' => $item['summary'],
        ]);
        $mentorStrip = collect($program['mentors'] ?? [])->chunk(2);
        $resourceStrip = $resources->chunk(2);
        $programTrack = $trackMap[$program['slug']] ?? 'Program Unggulan';
    @endphp

    <main class="relative z-10">
        <section class="mx-auto max-w-6xl px-6 pt-20 pb-12">
            <div class="grid gap-8 rounded-[44px] border border-white/40 bg-white/95 p-10 shadow-[0_55px_110px_-70px_rgba(8,23,41,0.9)] backdrop-blur lg:grid-cols-[1.15fr,0.85fr]">
                <div class="flex flex-col gap-6">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br {{ $program['gradient'] ?? 'from-teal-500/20 to-cyan-200/40' }} text-4xl">
                            {{ $program['emoji'] ?? '✨' }}
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.5em] text-brand-teal">{{ $programTrack }}</p>
                            <h1 class="font-heading text-4xl text-brand-ink lg:text-[44px]">{{ $program['title'] }}</h1>
                            <p class="text-sm text-slate-500">{{ $program['tagline'] }}</p>
                        </div>
                    </div>
                    <p class="text-base leading-relaxed text-slate-600">{{ $program['description'] }}</p>
                    <div class="grid gap-4 md:grid-cols-3">
                        @foreach($heroStats as $stat)
                            <div class="rounded-[26px] border border-brand-sky/45 bg-brand-mist p-4">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.35em] text-brand-teal">{{ $stat['label'] }}</p>
                                <p class="mt-2 font-heading text-2xl text-brand-ink">{{ $stat['value'] }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ $program['cta']['register_url'] ?? '#' }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-full bg-brand-teal px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-teal/40 transition hover:brightness-90">Gabung sesi ini</a>
                        <a href="{{ $program['cta']['contact_url'] ?? '#' }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-full border border-brand-teal/40 px-6 py-3 text-sm font-semibold text-brand-ink hover:border-brand-ink">Tanya {{ $program['cta']['contact_label'] ?? 'admin program' }}</a>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute -inset-4 rounded-[36px] bg-gradient-to-br {{ $program['gradient'] ?? 'from-teal-500/20 to-cyan-200/40' }} blur-3xl opacity-60"></div>
                    <div class="relative rounded-[36px] overflow-hidden border border-white/50 shadow-2xl">
                        <img src="{{ $program['hero_image'] }}" alt="{{ $program['title'] }} highlight" class="h-full w-full object-cover" loading="lazy">
                    </div>
                </div>
            </div>
        </section>

        @if($moduleTimeline->isNotEmpty())
            <section class="mx-auto max-w-6xl px-6 pb-16">
                <div class="rounded-[40px] border border-white/30 bg-white p-10 shadow-[0_45px_95px_-60px_rgba(8,23,41,0.55)]">
                    <p class="text-xs font-semibold uppercase tracking-[0.5em] text-brand-teal">Program Storyboard</p>
                    <h2 class="mt-2 font-heading text-3xl text-brand-ink">Dari orientasi hingga showcase.</h2>
                    <div class="mt-8 space-y-5">
                        @foreach($moduleTimeline as $module)
                            <div class="flex flex-col gap-4 rounded-[32px] border border-brand-sky/45 bg-brand-mist p-6 lg:flex-row lg:items-center lg:justify-between">
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.35em] text-brand-teal">Modul {{ str_pad($module['index'], 2, '0', STR_PAD_LEFT) }}</p>
                                    <h3 class="font-heading text-2xl text-brand-ink">{{ $module['title'] }}</h3>
                                </div>
                                <div class="lg:text-right">
                                    <p class="text-sm font-semibold text-brand-ink">{{ $module['duration'] }}</p>
                                    <p class="text-sm text-slate-600">{{ $module['description'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="mx-auto max-w-6xl px-6 pb-16">
            <div class="rounded-[40px] border border-white/30 bg-white p-10 shadow-[0_40px_90px_-55px_rgba(8,23,41,0.45)]">
                <p class="text-xs font-semibold uppercase tracking-[0.5em] text-brand-teal">Inisiatif Lapangan</p>
                <h2 class="mt-2 font-heading text-3xl text-brand-ink">Rangkaian program yang membentuk kultur.</h2>
                <div class="mt-8 space-y-6">
                    @foreach($initiativeStory as $story)
                        <div class="flex flex-col gap-4 rounded-[32px] border border-brand-sky/45 bg-brand-mist p-6 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.35em] text-brand-teal">{{ $story['meta'] }}</p>
                                <h3 class="font-heading text-2xl text-brand-ink">{{ $story['title'] }}</h3>
                                <p class="mt-3 text-sm text-slate-600">{{ $story['body'] }}</p>
                            </div>
                            <div class="rounded-full border border-brand-teal/35 px-5 py-2 text-xs font-semibold text-brand-ink">PIC: {{ $story['owner'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-6 pb-16">
            <div class="grid gap-8 rounded-[34px] border border-white bg-white p-10 shadow-[0_30px_60px_-50px_rgba(10,43,78,0.4)] lg:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.45em] text-brand-teal">Outcome & KPI</p>
                    <ul class="mt-6 space-y-4">
                        @foreach($outcomes as $outcome)
                            <li class="flex items-start gap-3">
                                <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full bg-brand-sky/25 text-brand-teal">✓</span>
                                <p class="text-sm text-slate-600">{{ $outcome }}</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="rounded-[28px] border border-brand-sky/45 bg-brand-mist p-8">
                    <p class="text-xs font-semibold uppercase tracking-[0.45em] text-brand-teal">Panel Monitoring</p>
                    <div class="mt-6 grid gap-4">
                        @foreach($highlights as $stat)
                            <div class="rounded-2xl border border-white bg-white/80 p-4 shadow-[0_20px_40px_-35px_rgba(10,43,78,0.4)]">
                                <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
                                <p class="font-heading text-2xl text-brand-ink">{{ $stat['value'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-6 pb-16">
            <div class="rounded-[38px] border border-white bg-white/95 p-10 shadow-[0_30px_60px_-45px_rgba(10,43,78,0.35)]">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.45em] text-brand-teal">Tim Penggerak</p>
                        <h2 class="font-heading text-2xl text-brand-ink">Mentor, coach, dan pengendali mutu.</h2>
                    </div>
                </div>
                <div class="mt-8 grid gap-6 md:grid-cols-2">
                    @foreach($mentorStrip as $pair)
                        <div class="flex gap-6 rounded-[28px] border border-brand-sky/45 bg-brand-mist p-6">
                            @foreach($pair as $mentor)
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $mentor['photo'] }}" alt="{{ $mentor['name'] }}" class="h-12 w-12 rounded-2xl object-cover" loading="lazy">
                                        <div>
                                            <p class="font-heading text-base text-brand-ink">{{ $mentor['name'] }}</p>
                                            <p class="text-xs font-semibold text-brand-teal">{{ $mentor['role'] }}</p>
                                        </div>
                                    </div>
                                    <p class="mt-3 text-xs text-slate-600">{{ $mentor['bio'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        @if($resources->isNotEmpty())
            <section class="mx-auto max-w-6xl px-6 pb-16">
                <div class="rounded-[38px] border border-white bg-white p-10 shadow-[0_30px_55px_-45px_rgba(10,43,78,0.35)]">
                    <div class="flex flex-col gap-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.45em] text-brand-teal">Resource Stack</p>
                        <h2 class="font-heading text-2xl text-brand-ink">Toolkit siap pakai untuk tim lapangan.</h2>
                    </div>
                    <div class="mt-8 space-y-6">
                        @foreach($resourceStrip as $group)
                            <div class="grid gap-6 md:grid-cols-2">
                                @foreach($group as $resource)
                                    <div class="rounded-3xl border border-brand-sky/45 bg-brand-mist p-6">
                                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-brand-teal">{{ $resource['type'] }}</p>
                                        <h3 class="mt-3 font-heading text-xl text-brand-ink">{{ $resource['title'] }}</h3>
                                        <p class="mt-2 text-sm text-slate-600">{{ $resource['description'] }}</p>
                                        <a href="{{ $resource['link'] }}" class="mt-4 inline-flex items-center text-sm font-semibold text-brand-teal hover:text-brand-ink">Lihat sumber →</a>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if($relatedPrograms->isNotEmpty())
            <section class="mx-auto max-w-6xl px-6 pb-24">
                <div class="flex flex-col gap-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.45em] text-brand-teal">Divisi Lain</p>
                    <h2 class="font-heading text-2xl text-brand-ink">Eksplorasi ekosistem BOM-PAI yang lain.</h2>
                </div>
                <div class="mt-8 flex snap-x gap-4 overflow-x-auto pb-4">
                    @foreach($relatedPrograms as $related)
                        <a href="{{ route('program.show', $related['slug']) }}" class="snap-start rounded-[32px] border border-white bg-white/95 px-6 py-5 shadow-[0_20px_45px_-35px_rgba(10,43,78,0.45)] min-w-[260px]">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl">{{ $related['emoji'] ?? '✨' }}</span>
                                <div>
                                    <p class="font-heading text-lg text-brand-ink">{{ $related['title'] }}</p>
                                    <p class="text-xs text-slate-500">{{ $related['tagline'] }}</p>
                                </div>
                            </div>
                            <p class="mt-4 text-xs text-slate-600">{{ \Illuminate\Support\Str::limit($related['description'], 100) }}</p>
                            <span class="mt-4 inline-flex items-center text-sm font-semibold text-brand-teal">Lihat detail →</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    @include('landing.partials.footer')
@endsection
