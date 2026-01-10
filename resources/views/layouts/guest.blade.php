<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-body text-brand-ink">
        <div class="relative min-h-screen overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-full bg-gradient-to-br from-brand-ink via-brand-ink/90 to-brand-teal/70 opacity-95"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(153,238,255,0.22),_transparent_62%)]"></div>

            <main class="relative z-10 flex min-h-screen items-center justify-center p-6">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
