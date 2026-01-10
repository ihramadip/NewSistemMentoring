<aside id="main-sidebar" class="w-64 flex-col bg-brand-ink text-white rounded-tr-2xl rounded-br-2xl">
    <!-- Logo -->
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-6">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10">
            <img src="/images/bompai.png" alt="Logo" class="h-10 w-12 object-contain">
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
            @php
                $dashboardRoute = 'dashboard'; // Default for mentee
                if (Auth::check()) {
                    if (Auth::user()->role->name === 'Admin') {
                        $dashboardRoute = 'admin.dashboard';
                    } elseif (Auth::user()->role->name === 'Mentor') {
                        $dashboardRoute = 'mentor.dashboard';
                    }
                }
            @endphp
            <a href="{{ route($dashboardRoute) }}" class="mt-1 flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs($dashboardRoute) ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25a2.25 2.25 0 01-2.25-2.25v-2.25z" /></svg>
                <span>Dashboard</span>
            </a>
        </div>

        @auth
        @if(Auth::user()->role->name === 'Admin')
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
                                                 </a>
                                                 <a href="{{ route('admin.exams.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.exams.*') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12H12m-2.25 4.5H12M12 18.75V15m-1.5 2.25l-1.5-1.5m1.5 1.5l1.5-1.5M12 18.75L10.5 17.25M12 18.75L13.5 17.25M12 14.25h-2.25M15 11.25H9M15 12h-2.25" />
                                                    </svg>
                                                    <span>Manajemen Ujian</span>
                                                </a>
                                                <a href="{{ route('admin.mentoring-groups.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.mentoring-groups.*') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962A3 3 0 0115 9.185a3 3 0 01-4.5 2.72m-7.5-2.962a3 3 0 00-4.682 2.72 8.982 8.982 0 003.741.479M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span>Manajemen Kelompok Mentoring</span>
                                                </a>
                                                <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-white/10">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 019 9v.375M10.125 2.25A3.375 3.375 0 0113.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 013.375 3.375M9 15l2.25 2.25L15 12" /></svg>
                                                    <span>Laporan & Statistik</span>
                                                </a>
                <a href="{{ route('admin.announcements.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('admin.announcements.*') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688 0-1.25-.562-1.25-1.25s.562-1.25 1.25-1.25h3.32c.688 0 1.25.562 1.25 1.25s-.562 1.25-1.25 1.25h-3.32zM9 19.5h6" /></svg>
                    <span>Pengumuman</span>
                </a>
            </div>
            
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
        @endif

        @if(Auth::user()->role->name === 'Mentor' || Auth::user()->role->name === 'Admin')
        <!-- Menu Mentor -->
        <div>
            <h2 class="px-2 text-xs font-semibold uppercase tracking-wider text-white/60">Navigasi Mentor</h2>
            <div class="mt-1 space-y-1">
                <a href="{{ route('mentor.groups.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('mentor.groups.*') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.028.658a.79.79 0 01-.588.792l-.028.008a.786.786 0 01-.766-.36zM14 8a2 2 0 11-4 0 2 2 0 014 0zM10 18a6.484 6.484 0 005.065-2.915 3 3 0 01-4.308-3.516.78.78 0 01.358-.442l.028.008a.79.79 0 01.588.792.786.786 0 01-.766.36 4.486 4.486 0 01-1.905 3.959c-.023.222-.014.442.028.658a.79.79 0 01-.588.792l-.028.008a.786.786 0 01-.766-.36z" />
                    </svg>
                    <span>Kelompok Bimbingan</span>
                </a>
                <a href="{{ route('mentor.sessions.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('mentor.sessions.*') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v1.755S5.47 5.1 7 5.914V4a1 1 0 112 0v1.914S10.47 5.1 12 5.914V4a1 1 0 112 0v1.914S15.47 5.1 17 5.914V4a1 1 0 112 0v2.414c0 .265-.105.52-.293.707l-2.414 2.414-2.415-2.414a1 1 0 01-.292-.707V4a1 1 0 10-2 0v1.914S10.47 5.1 12 5.914V4a1 1 0 10-2 0v1.914S8.47 5.1 7 5.914V4a1 1 0 00-2 0v1.914S3.47 5.1 2 5.914V3a1 1 0 011-1zm0 8a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zm0 4a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Sesi & Laporan</span>
                </a>
                <a href="{{ route('mentor.reports.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('mentor.reports.*') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 2.75a.75.75 0 00-1.5 0v8.5a.75.75 0 001.5 0v-8.5zM10 20a9.5 9.5 0 100-19 9.5 9.5 0 000 19zM10 1.5a8 8 0 110 16 8 8 0 010-16z" />
                        <path d="M13.28 5.72a.75.75 0 00-1.06-1.06l-4.5 4.5a.75.75 0 000 1.06l4.5 4.5a.75.75 0 101.06-1.06L9.28 10.5l4-4.78z" />
                    </svg>
                    <span>Progres Mentee</span>
                </a>
            </div>
        </div>
        @endif

        @if(Auth::user()->role->name === 'Mentee' || Auth::user()->role->name === 'Admin')
        <!-- Menu Mentee -->
        <div>
            <h2 class="px-2 text-xs font-semibold uppercase tracking-wider text-white/60">Mentee</h2>
            <div class="mt-1 space-y-1">

                <a href="{{ route('mentee.materials.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('mentee.materials.index') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                    <span>Materi Belajar</span>
                </a>
                <a href="{{ route('mentee.announcements.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('mentee.announcements.index') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688 0-1.25-.562-1.25-1.25s.562-1.25 1.25-1.25h3.32c.688 0 1.25.562 1.25 1.25s-.562 1.25-1.25 1.25h-3.32zM9 19.5h6" /></svg>
                    <span>Pengumuman</span>
                </a>
                <a href="{{ route('mentee.group.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('mentee.group.index') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962A3 3 0 0115 9.185a3 3 0 01-4.5 2.72m-7.5-2.962a3 3 0 00-4.682 2.72 8.982 8.982 0 003.741.479M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>Kelompok Mentoring Saya</span>
                </a>
                <a href="{{ route('mentee.sessions.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('mentee.sessions.index') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                    <span>Sesi Mentoring Saya</span>
                </a>
                <a href="{{ route('mentee.report.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('mentee.report.index') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                    <span>Laporan Hasil Mentoring</span>
                </a>
                <a href="{{ route('mentee.exams.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('mentee.exams.index') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12H12m-2.25 4.5H12M12 18.75V15m-1.5 2.25l-1.5-1.5m1.5 1.5l1.5-1.5M12 18.75L10.5 17.25M12 18.75L13.5 17.25M12 14.25h-2.25M15 11.25H9M15 12h-2.25" />
                    </svg>
                    <span>Ujian Saya</span>
                </a>

                <a href="{{ route('placement-test.create') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 {{ request()->routeIs('placement-test.create') ? 'bg-brand-teal' : 'hover:bg-white/10' }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 100 15 7.5 7.5 0 000-15z" /><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.25-5.25" /></svg>
                    <span>Ikuti Placement Test</span>
                </a>
            </div>
        </div>
        @endif

        {{-- Visible to all authenticated users --}}
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
        @endauth
    </nav>
</aside>
