<section id="kontak" class="bg-white px-6 py-24">
    <div class="mx-auto max-w-6xl">
        <div class="grid gap-12 lg:grid-cols-2">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.6em] text-[#1CA7A7]">Kontak & Media Sosial</p>
                <h3 class="section-title font-heading text-4xl text-[#0a2b4e]">Kami senang diajak kolaborasi.</h3>
                <p class="mt-4 text-slate-600">Hubungi kami untuk kebutuhan mentoring fakultas, narasumber kajian, sponsorship, hingga publikasi kegiatan mahasiswa.</p>
                <div class="mt-8 grid gap-4">
                    @foreach ($contacts as $contact)
                        <a href="{{ $contact['link'] }}" class="flex items-center justify-between rounded-2xl border border-[#E3EDF3] px-5 py-4 text-sm text-[#0a2b4e] transition hover:border-[#1CA7A7]">
                            <div>
                                <p class="font-semibold">{{ $contact['label'] }}</p>
                                <p class="text-slate-600">{{ $contact['value'] }}</p>
                            </div>
                            <span>→</span>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="rounded-[36px] bg-[#0a2b4e] p-10 text-white">
                <p class="text-sm uppercase tracking-[0.6em] text-white/60">Hubungi Kami</p>
                <h4 class="font-heading text-3xl">Tinggalkan pesan singkat.</h4>
                <form class="mt-8 space-y-5">
                    <div>
                        <label class="text-xs uppercase tracking-[0.35em] text-white/60">Nama</label>
                        <input type="text" class="mt-2 w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm placeholder:text-white/40" placeholder="Nama lengkap" />
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.35em] text-white/60">Email</label>
                        <input type="email" class="mt-2 w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm placeholder:text-white/40" placeholder="Alamat email kampus" />
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.35em] text-white/60">Pesan</label>
                        <textarea rows="4" class="mt-2 w-full rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm placeholder:text-white/40" placeholder="Ceritakan kebutuhan kolaborasi"></textarea>
                    </div>
                    <button type="button" class="w-full rounded-full bg-white py-3 text-sm font-semibold text-[#0a2b4e]">Kirim Pesan</button>
                </form>
            </div>
        </div>
    </div>
</section>
