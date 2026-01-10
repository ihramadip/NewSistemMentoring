<aside id="main-sidebar" class="w-64 flex-col bg-brand-ink text-white rounded-tr-2xl rounded-br-2xl">
    <!-- Logo -->
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-6">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10">
            <img src="/images/bompai.png" alt="Logo" class="h-10 w-10 object-contain">
        </div>
        <div>
            <p class="text-xs uppercase tracking-[0.4em] text-white/70">BOM-PAI</p>
            <h1 class="font-heading text-lg font-semibold">Sistem Mentoring</h1>
        </div>
    </a>

    <nav class="flex-grow space-y-4 p-4">
        <!-- Dashboard -->
        <div>
            <h2 class="px-2 text-xs font-semibold uppercase tracking-wider text-white/60">Utama</h2>
            <a href="{{ route('dashboard') }}" class="mt-1 flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('dashboard') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25a2.25 2.25 0 01-2.25-2.25v-2.25z" /></svg>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- Menu Admin -->
        <div>
            <h2 class="px-2 text-xs font-semibold uppercase tracking-wider text-white/60">Admin</h2>
            <div class="mt-1 space-y-1">
                <a href="{{ route('admin.mentor-applications.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.mentor-applications.*') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962A3 3 0 0115 9.185a3 3 0 01-4.5 2.72m-7.5-2.962a3 3 0 00-4.682 2.72 8.982 8.982 0 003.741.479M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>Manajemen Pementor</span>
                </a>
                <a href="{{ route('admin.mentees.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.mentees.*') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-4.67c.12-.313.253-.617.4-1.022a4.125 4.125 0 00-7.533-2.493c-3.693 0-6.105 2.818-6.105 6.375a6.375 6.375 0 004.121 5.952v-.003c1.113 0 2.16-.285 3.07-.786z" /></svg>
                    <span>Manajemen Mentee</span>
                </a>
                <a href="{{ route('admin.placement-tests.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.placement-tests.*') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 100 15 7.5 7.5 0 000-15z" /><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.25-5.25" /></svg>
                    <span>Placement Test</span>
                </a>
                                                 <a href="{{ route('admin.materials.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.materials.*') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                                                     <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 1.087M3.75 9h16.5" /></svg>
                                                     <span>Manajemen Materi</span>
                                                 </a>                                 <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-white/10">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 019 9v.375M10.125 2.25A3.375 3.375 0 0113.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 013.375 3.375M9 15l2.25 2.25L15 12" /></svg>
                                    <span>Laporan & Statistik</span>
                                </a>
                                 <a href="{{ route('admin.announcements.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.announcements.*') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688 0-1.25-.562-1.25-1.25s.562-1.25 1.25-1.25h3.32c.688 0 1.25.562 1.25 1.25s-.562 1.25-1.25 1.25h-3.32zM9 19.5h6" /></svg>
                                    <span>Pengumuman</span>
                                </a>            </div>
            
            <div class="mt-4">
                <h3 class="px-2 text-xs font-semibold uppercase tracking-wider text-white/60">Data Master</h3>
                <a href="{{ route('admin.faculties.index') }}" class="mt-1 flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.faculties.*') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 1.087M3.75 9h16.5" /></svg>
                    <span>Manajemen Fakultas</span>
                </a>
                <a href="{{ route('admin.levels.index') }}" class="mt-1 flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.levels.*') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12" /></svg>
                    <span>Manajemen Level</span>
                </a>
            </div>
        </div>

        <!-- Menu Mentor -->
        <div>
            <h2 class="px-2 text-xs font-semibold uppercase tracking-wider text-white/60">Mentor</h2>
            <div class="mt-1 space-y-1">
                <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-white/10">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.153 1.586m-5.8 0c-.379.39-.75.817-1.084 1.287m1.084-1.287A3 3 0 0113.5 3.75h.618a3 3 0 012.834 2.19m-6.25 16.5A3.375 3.375 0 016.75 18h-1.5a3.375 3.375 0 01-3.375-3.375V6.108c0-1.135.845-2.098 1.976-2.192a48.424 48.424 0 011.123-.08m3.842 16.5-1.084-1.287" /></svg>
                    <span>Laporan Pertemuan</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-white/10">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" /></svg>
                    <span>Progress Mentee</span>
                </a>
            </div>
        </div>

        <!-- Menu Mentee -->
        <div>
            <h2 class="px-2 text-xs font-semibold uppercase tracking-wider text-white/60">Mentee</h2>
            <div class="mt-1 space-y-1">
                <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-white/10">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                    <span>Jadwal & Sesi</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-white/10">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                    <span>Materi Belajar</span>
                </a>
                <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-white/10">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0l-3.32-3.319A6.375 6.375 0 016.75 4.5h10.5a6.375 6.375 0 014.567 2.328l-3.32 3.319m-3.247 3.742c-.21.242-.42.48-.633.712A6.746 6.746 0 0012 15.54a6.746 6.746 0 00-1.897-2.343c-.213-.232-.423-.47-.633-.712m3.794 0V12h-3v-1.258h3z" /></svg>
                    <span>Ujian Akhir</span>
                </a>
            </div>
        </div>

        <!-- Menu Umum -->
        <div>
            <h2 class="px-2 text-xs font-semibold uppercase tracking-wider text-white/60">Umum</h2>
            <div class="mt-1 space-y-1">
                <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-white/10">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.76 9.76 0 01-2.53-.423m-7.758-11.828a9.753 9.753 0 01-2.083-5.228c0-4.556 4.03-8.25 9-8.25a9.753 9.753 0 012.083 5.228m-7.758 11.828c-3.181 0-5.758-2.577-5.758-5.758s2.577-5.758 5.758-5.758c3.181 0 5.758 2.577 5.758 5.758s-2.577 5.758-5.758 5.758z" /></svg>
                    <span>Forum Diskusi</span>
                </a>
            </div>
        </div>
    </nav>
</aside>
