

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>BOM-PAI UNISBA — Membina Kepribadian Islami</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-body bg-brand-mist text-brand-ink">
        <div class="relative overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-[620px] bg-gradient-to-br from-brand-ink via-brand-ink/90 to-brand-teal/70 opacity-95"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(153,238,255,0.22),_transparent_62%)]"></div>

            @include('landing.partials.nav')

            <main class="relative z-10">
                @include('landing.partials.hero')
                @include('landing.partials.about')
                @include('landing.partials.programs')
                @include('landing.partials.documentation')
                @include('landing.partials.blog')
                @include('landing.partials.portal')
                @include('landing.partials.alumni')
                @include('landing.partials.contact')
            </main>

            @include('landing.partials.footer')
        </div>
    </body>
</html>
