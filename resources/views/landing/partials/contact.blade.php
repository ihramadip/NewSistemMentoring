<section id="kontak" class="bg-white px-6 py-24">
    <div class="mx-auto max-w-6xl">
        <div class="grid gap-12 lg:grid-cols-2">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.6em] text-brand-teal">Kontak & Media Sosial</p>
                <h3 class="section-title font-heading text-4xl text-brand-ink">Kami senang diajak kolaborasi.</h3>
                <p class="mt-4 text-slate-600">Hubungi kami untuk kebutuhan mentoring fakultas, narasumber kajian, sponsorship, hingga publikasi kegiatan mahasiswa.</p>
                <div class="mt-8 grid gap-4">
                    @foreach ($contacts as $contact)
                        <a href="{{ $contact['link'] }}" class="flex items-center justify-between rounded-2xl border border-brand-sky/45 px-5 py-4 text-sm text-brand-ink transition hover:border-brand-teal">
                            <div>
                                <p class="font-semibold">{{ $contact['label'] }}</p>
                                <p class="text-slate-600">{{ $contact['value'] }}</p>
                            </div>
                            <span>→</span>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="rounded-[36px] bg-gradient-to-br from-brand-ink via-brand-ink/90 to-brand-teal/80 p-10 text-brand-mist shadow-[0_30px_64px_-54px_rgba(15,23,42,0.5)]">
                <p class="text-sm uppercase tracking-[0.6em] text-white/65">Hubungi Kami</p>
                <h4 class="font-heading text-3xl text-white">Tinggalkan pesan singkat.</h4>
                <form class="mt-8 space-y-5">
                    <div>
                        <label class="text-xs uppercase tracking-[0.35em] text-white/70">Nama</label>
                        <input type="text" class="mt-2 w-full rounded-2xl border border-brand-sky/50 bg-white/80 px-4 py-3 text-sm text-brand-ink placeholder:text-brand-ink/40" placeholder="Nama lengkap" />
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.35em] text-white/70">Email</label>
                        <input type="email" class="mt-2 w-full rounded-2xl border border-brand-sky/50 bg-white/80 px-4 py-3 text-sm text-brand-ink placeholder:text-brand-ink/40" placeholder="Alamat email kampus" />
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.35em] text-white/70">Pesan</label>
                        <textarea rows="4" class="mt-2 w-full rounded-2xl border border-brand-sky/50 bg-white/80 px-4 py-3 text-sm text-brand-ink placeholder:text-brand-ink/40" placeholder="Ceritakan kebutuhan kolaborasi"></textarea>
                    </div>
                    <button type="button" class="w-full rounded-full bg-white/95 py-3 text-sm font-semibold text-brand-ink shadow-[0_10px_22px_-16px_rgba(255,255,255,0.6)]">Kirim Pesan</button>
                </form>
            </div>
        </div>
    </div>
</section>
