## Palet warna baru

- `brand-sky`: `#99eeff`
- `brand-teal`: `#1ca7a7`
- `brand-gold`: `#d4a017`
- `brand-mist`: `#f7f9fa`
- `brand-ink`: `#0f172a`

## File & contoh penerapan

- `tailwind.config.js`
  - Menambah `theme.extend.colors.brand` dengan nilai di atas.

- `resources/views/landing/layout.blade.php`
  - Body background dan hero overlay pakai gradasi ink → teal, radial brand-sky tipis.

- `resources/views/landing/partials/nav.blade.php`
  - Tombol CTA: `bg-brand-teal` + `shadow-brand-teal/30` dengan hover subtle.

- `resources/views/landing/partials/hero.blade.php`
  - CTA utama: `bg-brand-teal` dengan shadow lembut.
  - Kartu statistik: border `brand-sky/35`, teks `brand-teal`, heading `brand-ink`.
  - Panel kaca: border `brand-sky/35`, gradient tipis sky/ink.
  - Panel fitur: gradient `from-brand-ink via-brand-teal to-brand-sky/70`.

- `resources/views/landing/partials/programs.blade.php`
  - Section bg `brand-mist`, headline `brand-ink`, badge/link `brand-teal`/`brand-gold`.

- `resources/views/landing/partials/documentation.blade.php`
  - Headline `brand-ink`, border kartu `brand-sky/40`, link `brand-teal`.

- `resources/views/landing/partials/blog.blade.php`
  - Section bg `brand-mist`, kategori badge `brand-sky/20` + `text-brand-teal`, judul `brand-ink`.

- `resources/views/landing/partials/alumni.blade.php`
  - Section bg `brand-mist`, headline `brand-ink`, garis pemisah `brand-sky/40`.

- `resources/views/landing/partials/about.blade.php`
  - Shadow utama turunkan ink/teal, headline `brand-ink`, kartu border `brand-sky/40`, label `brand-teal`.

- `resources/views/landing/partials/portal.blade.php`
  - Gradient utama gelap `brand-ink → brand-teal/80`, CTA putih/ink.
  - Panel kaca border `brand-sky/30`, bullet `brand-gold/18`.

- `resources/views/landing/partials/contact.blade.php`
  - Gradient gelap `brand-ink → brand-teal/80`, teks putih.
  - Label/input contoh:
    ```html
    <label class="text-xs uppercase tracking-[0.35em] text-white/70">Email</label>
    <input
        type="email"
        class="mt-2 w-full rounded-2xl border border-brand-sky/50 bg-white/80 px-4 py-3 text-sm text-brand-ink placeholder:text-brand-ink/40"
        placeholder="Alamat email kampus"
    />
    ```

- `resources/views/landing/partials/footer.blade.php`
  - Footer `bg-brand-ink`, teks `brand-mist`, border links `brand-sky/40`.

- `resources/views/program/layout.blade.php`
  - Hero overlay gradient gelap ink → teal, radial sky redup.

- `resources/views/program/index.blade.php`
  - CTA/hero: `bg-brand-teal` buttons, stats border `brand-sky/45`, texts `brand-ink`.
  - Tracks pill bg `brand-sky/20`, operational icons `brand-sky/25` + `text-brand-teal`.
  - Closing banner gradient gelap ink → teal, CTA putih/ink.

- `resources/views/program/show.blade.php`
  - Headline/labels `brand-teal`, text `brand-ink`, cards border `brand-sky/45`, buttons teal/ink, badges gold/sky toned.

- `resources/views/landing/partials/programs.blade.php` & reused gradients in `landing/program layout` data arrays
  - Gradients updated to brand-sky/brand-teal/brand-gold mixes with reduced opacity.
