<section id="portal" class="bg-white px-6 py-24" x-data="{ modalOpen: false }">
    <div class="mx-auto max-w-6xl rounded-[36px] bg-gradient-to-r from-brand-ink via-brand-ink/90 to-brand-teal/80 p-10 text-white">
        <div class="flex flex-col gap-10 lg:flex-row lg:items-center">
            <div class="lg:w-2/3">
                <p class="text-sm uppercase tracking-[0.6em] text-white/70">Portal Mentoring</p>
                <h3 class="font-heading text-4xl leading-tight">Satu pintu untuk pendaftaran, monitoring, dan laporan mentoring.</h3>
                <p class="mt-4 text-base text-white/85">
                    Navigasi cepat ke pendaftaran mentee, registrasi pementor, jadwal pertemuan, nilai, hingga dashboard analitik progres. Portal berada pada sub path `portal.bom-pai.or.id` sehingga domain utama tetap fokus pada branding publik.
                </p>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-white/15 p-4">
                        <p class="text-sm font-semibold">Role Mentee</p>
                        <p class="mt-2 text-xs text-white/80">Pendaftaran, penempatan kelompok, jurnal refleksi.</p>
                    </div>
                    <div class="rounded-2xl bg-white/15 p-4">
                        <p class="text-sm font-semibold">Role Pementor</p>
                        <p class="mt-2 text-xs text-white/80">Upload materi, nilai, monitoring hadir.</p>
                    </div>
                    <div class="rounded-2xl bg-white/15 p-4">
                        <p class="text-sm font-semibold">Role Pengurus</p>
                        <p class="mt-2 text-xs text-white/80">Dasbor KPI, approval program, laporan otomatis.</p>
                    </div>
                    <div class="rounded-2xl bg-white/15 p-4">
                        <p class="text-sm font-semibold">Publik</p>
                        <p class="mt-2 text-xs text-white/80">Download SOP, jadwal kajian, publikasi narasumber.</p>
                    </div>
                </div>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('login') }}" class="rounded-full bg-white/95 px-6 py-3 text-sm font-semibold text-brand-ink shadow-[0_14px_28px_-22px_rgba(255,255,255,0.4)]">Masuk Portal</a>
                    <button @click="modalOpen = true" type="button" class="rounded-full border border-white/70 px-6 py-3 text-sm font-semibold text-white/90">Daftar</button>
                </div>
            </div>
            <div class="glass-panel rounded-3xl border border-brand-sky/30 bg-gradient-to-br from-brand-mist via-brand-sky/12 to-brand-ink/5 p-6 text-brand-ink shadow-[0_28px_56px_-46px_rgba(15,23,42,0.48)] backdrop-blur lg:w-1/3">
                <p class="text-sm font-semibold text-brand-teal">Integrasi Dokumen</p>
                <ul class="mt-4 space-y-3 text-sm text-slate-700">
                    <li class="flex items-start gap-3">
                        <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-brand-gold/18 text-brand-gold">•</span>
                        <span>Pengumuman mentoring & jadwal TFM</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-brand-gold/18 text-brand-gold">•</span>
                        <span>Rekrutmen pementor & pengurus</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-brand-gold/18 text-brand-gold">•</span>
                        <span>Laporan sosial & sponsorship</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-brand-gold/18 text-brand-gold">•</span>
                        <span>Template SOP standar</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div x-show="modalOpen" style="display: none;" @keydown.escape.window="modalOpen = false" x-transition.opacity.duration.300ms class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
        <div @click.outside="modalOpen = false" class="relative w-full max-w-lg rounded-2xl bg-white p-8 shadow-xl">
            <button @click="modalOpen = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <h3 class="font-heading text-2xl font-semibold text-brand-ink">Pilih Tipe Pendaftaran</h3>
            <p class="mt-2 text-slate-600">Silakan pilih jenis pendaftaran yang sesuai dengan peran Anda.</p>

            <div class="mt-6 space-y-4">
                <a href="{{ route('register') }}" class="block w-full rounded-lg border border-gray-200 bg-gray-50 p-4 text-left transition hover:bg-gray-100">
                    <p class="font-semibold text-brand-ink">Daftar sebagai Mentee</p>
                    <p class="text-sm text-slate-500">Untuk mahasiswa yang ingin mengikuti program mentoring.</p>
                </a>
                <a href="{{ route('mentor.register.create') }}" class="block w-full rounded-lg border border-gray-200 bg-gray-50 p-4 text-left transition hover:bg-gray-100">
                    <p class="font-semibold text-brand-ink">Daftar sebagai Pementor</p>
                    <p class="text-sm text-slate-500">Untuk Anda yang ingin berkontribusi sebagai pembimbing.</p>
                </a>
                <a href="#" class="block w-full rounded-lg border border-gray-200 bg-gray-50 p-4 text-left transition hover:bg-gray-100 opacity-50 cursor-not-allowed">
                    <p class="font-semibold text-brand-ink">Daftar sebagai Pengurus</p>
                    <p class="text-sm text-slate-500">Pendaftaran untuk kepengurusan saat ini belum dibuka.</p>
                </a>
            </div>
        </div>
    </div>
</section>
