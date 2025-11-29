<section id="tentang" class="relative -mt-14 rounded-t-[40px] bg-white px-6 pb-24 pt-20 shadow-[0_-40px_120px_-60px_rgba(10,43,78,0.5)]">
    <div class="mx-auto max-w-6xl">
        <div class="flex flex-col gap-10 lg:flex-row lg:items-start">
            <div class="lg:w-1/2">
                <p class="text-sm font-semibold uppercase tracking-[0.6em] text-[#1CA7A7]">Tentang Organisasi</p>
                <h3 class="section-title font-heading text-4xl text-[#0a2b4e]">BOM-PAI hadir sebagai wajah resmi pembinaan mahasiswa Islami UNISBA.</h3>
                <p class="mt-6 text-lg text-slate-600">
                    Sejak 2001 kami mengelola Gerakan Bina Mentoring Organisasi (GBMO) dengan pendekatan kepengurusan profesional. Visi kami sederhana: memastikan setiap mahasiswa muslim mendapatkan teladan akhlak, kompetensi, dan jejaring kebaikan.
                </p>
                <div class="mt-6 grid gap-4">
                    <div class="rounded-2xl border border-[#E3EDF3] p-5">
                        <p class="text-xs uppercase tracking-[0.4em] text-slate-500">Visi</p>
                        <p class="mt-3 text-lg text-slate-700">Menjadi lembaga mentoring Islami paling relevan bagi generasi kampus modern.</p>
                    </div>
                    <div class="rounded-2xl border border-[#E3EDF3] p-5">
                        <p class="text-xs uppercase tracking-[0.4em] text-slate-500">Misi</p>
                        <ul class="mt-3 space-y-2 text-slate-700">
                            <li>• Mengintegrasikan nilai Qurani dalam aktivitas akademik.</li>
                            <li>• Menyiapkan mentor dengan standar pedagogi Islami kontemporer.</li>
                            <li>• Menghadirkan platform digital yang transparan dan inklusif.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="lg:w-1/2">
                <div class="grid gap-4">
                    @foreach ([
                        ['title' => 'Struktur Kepengurusan', 'detail' => 'Ketua umum, sekretaris, bendahara, dan 6 departemen strategis.'],
                        ['title' => 'GBMO', 'detail' => 'Framework mentoring berbasis kurikulum karakter Islami dengan modul digital.'],
                        ['title' => 'Departemen', 'detail' => 'Mentoring, PSDM, Kajian, Humas, Sosial, dan Kewirausahaan.'],
                    ] as $item)
                        <div class="glass-panel rounded-3xl border border-[#E3EDF3] px-6 py-5">
                            <p class="text-sm font-semibold text-[#1CA7A7]">{{ $item['title'] }}</p>
                            <p class="mt-2 text-base text-slate-600">{{ $item['detail'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
