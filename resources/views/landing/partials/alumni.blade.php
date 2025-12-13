<section id="alumni" class="bg-brand-mist px-6 py-24">
    <div class="mx-auto max-w-6xl">
        <div class="flex flex-col items-start justify-between gap-6 md:flex-row">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.6em] text-brand-teal">Profil Alumni & Testimoni</p>
                <h3 class="section-title font-heading text-4xl text-brand-ink">Cerita inspiratif lintas generasi.</h3>
            </div>
            <p class="max-w-lg text-slate-600">
                Bagian ini memperkuat kredibilitas jangka panjang. Alumni pementor dan peserta membagikan dampak personal maupun profesional setelah mengikuti BOM-PAI.
            </p>
        </div>
        <div class="mt-10 grid gap-6 md:grid-cols-3">
            @foreach ($testimonials as $testi)
                <div class="rounded-3xl border border-white bg-white p-6 shadow-[0_25px_45px_-35px_rgba(10,43,78,0.35)]">
                    <p class="text-sm text-slate-500">“{{ $testi['quote'] }}”</p>
                    <div class="mt-6 border-t border-brand-sky/40 pt-4">
                        <p class="font-heading text-lg text-brand-ink">{{ $testi['name'] }}</p>
                        <p class="text-xs uppercase tracking-[0.25em] text-slate-500">{{ $testi['role'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
