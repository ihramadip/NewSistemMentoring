<section id="dokumentasi" class="bg-white px-6 py-24">
    <div class="mx-auto max-w-6xl">
        <div class="flex flex-col items-start justify-between gap-6 md:flex-row">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.6em] text-[#1CA7A7]">Dokumentasi Kegiatan</p>
                <h3 class="section-title font-heading text-4xl text-[#0a2b4e]">Cerita visual yang konsisten.</h3>
            </div>
            <p class="max-w-lg text-slate-600">
                Landing page menghadirkan carousel foto, highlight bulanan, dan video singkat agar publik dapat merasakan energi kegiatan BOM-PAI secara langsung.
            </p>
        </div>
        <div class="mt-10">
            <div class="flex space-x-6 overflow-x-auto pb-4 scrollbar-hide">
                @foreach ($documentation as $doc)
                    <div class="flex-shrink-0 w-80 overflow-hidden rounded-3xl border border-[#E3EDF3]">
                        <div class="h-56 w-full bg-cover bg-center" style="background-image: url('{{ $doc['image'] }}');"></div>
                        <div class="p-5">
                            <p class="text-xs uppercase tracking-[0.4em] text-slate-500">{{ $doc['type'] }} · {{ $doc['month'] }}</p>
                            <h4 class="mt-3 font-heading text-xl text-[#0a2b4e]">{{ $doc['title'] }}</h4>
                            <p class="mt-2 text-sm text-slate-600">{{ $doc['description'] }}</p>
                            <button class="mt-4 text-sm font-semibold text-[#1CA7A7]">Lihat galeri →</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>