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
            <a href="#portal" class="rounded-full bg-brand-teal px-6 py-3 text-base font-semibold text-white shadow-lg shadow-brand-teal/25 transition hover:brightness-90">
                Masuk Portal Mentoring
            </a>
            <a href="#tentang" class="rounded-full border border-white/40 bg-white/10 px-6 py-3 text-base font-semibold text-white/90 transition hover:bg-white/15 hover:text-white">
                Kenali Organisasi
            </a>
        </div>
        <div class="mt-10 grid gap-6 sm:grid-cols-2">
            @foreach ($stats as $stat)
                <div class="relative overflow-hidden rounded-2xl border border-brand-sky/35 bg-gradient-to-br from-brand-mist via-brand-sky/12 to-brand-ink/5 p-5 text-brand-ink shadow-[0_18px_42px_-36px_rgba(15,23,42,0.45)]">
                    <div class="absolute inset-y-0 left-0 w-1 bg-brand-teal/70"></div>
                    <p class="text-sm uppercase tracking-[0.35em] text-brand-teal">{{ $stat['label'] }}</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-brand-ink">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
    <div class="glass-panel lg:w-2/5 rounded-3xl border border-brand-sky/35 bg-gradient-to-br from-brand-mist via-brand-sky/12 to-brand-mist p-[1px] shadow-[0_26px_58px_-46px_rgba(15,23,42,0.48)] backdrop-blur">
        <div class="rounded-[28px] bg-white p-8 text-brand-ink">
            <div class="inline-flex items-center gap-2 rounded-full bg-brand-gold/14 px-3 py-1 text-xs font-semibold text-brand-gold">Signature Message</div>
            <h3 class="mt-3 font-accent text-3xl text-brand-ink">"Khairunnas anfa'uhum linnas"</h3>
            <p class="mt-4 text-base text-slate-600">
                Portal ini memadukan nilai Islami dan profesionalitas kampus. Dari pendaftaran mentee, monitoring halaqah, sampai publikasi laporan, semua dikelola secara digital.
            </p>
            <div class="mt-6 rounded-2xl bg-gradient-to-br from-brand-ink via-brand-teal to-brand-sky/70 p-6 text-brand-mist shadow-[0_16px_34px_-30px_rgba(15,23,42,0.55)]">
                <p class="text-sm uppercase tracking-[0.3em] text-white/85">Inside the Portal</p>
                <ul class="mt-4 space-y-3 text-base">
                    <li class="flex items-center gap-3"><span class="text-brand-gold/80">•</span> Pendaftaran mentee & pementor</li>
                    <li class="flex items-center gap-3"><span class="text-brand-gold/80">•</span> Jadwal, nilai, dan rapor mentoring</li>
                    <li class="flex items-center gap-3"><span class="text-brand-gold/80">•</span> Dasbor kepengurusan realtime</li>
                    <li class="flex items-center gap-3"><span class="text-brand-gold/80">•</span> Dokumentasi & laporan publik</li>
                </ul>
            </div>
        </div>
    </div>
</section>
