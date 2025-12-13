<section id="blog" class="bg-brand-mist px-6 py-24">
    <div class="mx-auto max-w-6xl">
        <div class="flex flex-col items-start justify-between gap-6 md:flex-row">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.6em] text-brand-teal">Blog & Artikel</p>
                <h3 class="section-title font-heading text-4xl text-brand-ink">Kanal resmi publikasi.</h3>
            </div>
            <p class="max-w-lg text-slate-600">
                Semua pengumuman mentoring, liputan kegiatan, hingga artikel reflektif dipublikasikan di sini untuk memperkuat SEO dan kredibilitas BOM-PAI.
            </p>
        </div>
        <div class="mt-10 grid gap-6 md:grid-cols-3">
            @foreach ($blogPosts as $post)
                <article class="rounded-3xl border border-white bg-white p-6 shadow-[0_25px_45px_-35px_rgba(10,43,78,0.35)]">
                    <span class="rounded-full bg-brand-sky/20 px-3 py-1 text-xs font-semibold text-brand-teal">{{ $post['category'] }}</span>
                    <h4 class="mt-4 font-heading text-2xl text-brand-ink">{{ $post['title'] }}</h4>
                    <p class="mt-3 text-sm text-slate-600">{{ $post['excerpt'] }}</p>
                    <div class="mt-5 flex items-center justify-between text-xs text-slate-500">
                        <span>{{ $post['date'] }}</span>
                        <a href="#" class="font-semibold text-brand-teal">Baca →</a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
