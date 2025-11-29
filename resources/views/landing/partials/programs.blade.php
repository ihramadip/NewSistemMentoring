<section id="program" class="bg-[#F7F9FA] px-6 py-24">
    <div class="mx-auto max-w-6xl">
        <div class="flex flex-col items-start justify-between gap-6 md:flex-row">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.6em] text-[#1CA7A7]">Program Unggulan</p>
                <h3 class="section-title font-heading text-4xl text-[#0a2b4e]">Setiap departemen bersuara.</h3>
            </div>
            <p class="max-w-lg text-slate-600">
                Keenam departemen mendapatkan panggung yang sama besar. Landing page menampilkan highlight, indikator kinerja, dan tautan ke portal masing-masing program.
            </p>
        </div>
        <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($programs as $program)
                <div class="rounded-3xl border border-white bg-white p-6 shadow-[0_25px_45px_-35px_rgba(10,43,78,0.35)] transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br {{ $program['gradient'] }} text-xl">{{ $program['emoji'] }}</div>
                    <h4 class="mt-4 font-heading text-2xl text-[#0a2b4e]">{{ $program['title'] }}</h4>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $program['description'] }}</p>
                    <a href="/program" class="mt-5 inline-flex items-center text-sm font-semibold text-[#1CA7A7]">Selengkapnya →</a>
                </div>
            @endforeach
        </div>
    </div>
</section>
