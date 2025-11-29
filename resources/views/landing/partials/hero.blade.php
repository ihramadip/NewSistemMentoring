<section id="hero" class="mx-auto flex max-w-6xl flex-col gap-10 px-6 pb-28 pt-10 text-white lg:flex-row lg:items-center">
    <div class="lg:w-3/5">
        <p class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/20 px-4 py-1 text-xs uppercase tracking-[0.3em] text-white/80">
            Modern • Religius • Profesional
        </p>
        <h2 class="font-heading text-4xl leading-tight text-white sm:text-5xl">
            Membina Kepribadian Islami Mahasiswa UNISBA dengan pendekatan yang modern dan menyentuh hati.
        </h2>
        <p class="mt-6 text-lg text-white/85">
            Landing page ini menjadi wajah resmi BOM-PAI sekaligus gerbang menuju Portal Mentoring terpadu. Semua informasi publik, kegiatan, hingga rekaman program tersaji rapi dalam satu domain agar branding selalu konsisten.
        </p>
        <div class="mt-8 flex flex-wrap gap-4">
            <a href="#portal" class="rounded-full bg-[#1CA7A7] px-6 py-3 text-base font-semibold text-white shadow-xl shadow-teal-500/30 transition hover:bg-[#199090]">
                Masuk Portal Mentoring
            </a>
            <a href="#tentang" class="rounded-full bg-[#2f7a7aff] px-6 py-3 text-base font-semibold text-white/90 transition hover:border-white hover:text-white">
                Kenali Organisasi
            </a>
        </div>
        <div class="mt-10 grid gap-6 sm:grid-cols-2">
            @foreach ($stats as $stat)
                <div class="rounded-2xl p-4 backdrop-blur">
                    <p class="text-sm uppercase tracking-[0.35em]" style="color: #026b6bff;">{{ $stat['label'] }}</p>
                    <p class="mt-2 font-heading text-3xl font-semibold" style="color: #1CA7A7;">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
    <div class="glass-panel lg:w-2/5">
        <div class="rounded-3xl p-8 text-[#0c1b2e]">
            <p class="text-sm font-semibold text-[#1CA7A7]">Signature Message</p>
            <h3 class="font-accent text-3xl text-[#0a2b4e]">"Khairunnas anfa'uhum linnas"</h3>
            <p class="mt-4 text-base text-slate-600">
                Portal ini memadukan nilai Islami dan profesionalitas kampus. Dari pendaftaran mentee, monitoring halaqah, sampai publikasi laporan, semua dikelola secara digital.
            </p>
            <div class="mt-6 rounded-2xl bg-[#0a2b4e] p-6 text-white">
                <p class="text-sm uppercase tracking-[0.3em] text-white/70">Inside the Portal</p>
                <ul class="mt-4 space-y-3 text-base">
                    <li class="flex items-center gap-3"><span class="text-[#D4A017]">•</span> Pendaftaran mentee & pementor</li>
                    <li class="flex items-center gap-3"><span class="text-[#D4A017]">•</span> Jadwal, nilai, dan rapor mentoring</li>
                    <li class="flex items-center gap-3"><span class="text-[#D4A017]">•</span> Dasbor kepengurusan realtime</li>
                    <li class="flex items-center gap-3"><span class="text-[#D4A017]">•</span> Dokumentasi & laporan publik</li>
                </ul>
            </div>
        </div>
    </div>
</section>
