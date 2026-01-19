{{-- Layout utama untuk halaman dashboard mentee --}}
<x-app-layout>
    {{-- Slot header untuk menampilkan judul halaman --}}
    <x-slot name="header">
        <x-page-header title="{{ __('Dashboard Mentee') }}" subtitle="Ringkasan dan akses cepat ke semua informasi mentoring Anda.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25a2.25 2.25 0 01-2.25-2.25v-2.25z" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    {{-- Container utama halaman --}}
    <div class="py-12">
        {{-- Container dengan lebar maksimum dan margin horizontal otomatis --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Card utama dengan background putih dan shadow --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                {{-- Area konten utama --}}
                <div class="p-6 text-gray-900">
                    {{-- Judul selamat datang --}}
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Selamat Datang, <span class="text-brand-teal">{{ $user->name }}!</span></h3>

                    {{-- Area untuk menampilkan pesan flash (status, success, error, warning) --}}
                    @if(session('status'))
                        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('status') }}</span>
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="bg-teal-100 border border-teal-400 text-teal-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                    @if(session('warning'))
                        <div class="bg-orange-100 border border-orange-400 text-orange-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('warning') }}</span>
                        </div>
                    @endif

                    {{-- Grid untuk card-card informasi --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                        {{-- Card Status Tes Penempatan --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
                            <div>
                                <div class="flex items-center mb-3">
                                    <svg class="h-6 w-6 text-brand-teal mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0l-3.32-3.319A6.375 6.375 0 016.75 4.5h10.5a6.375 6.375 0 014.567 2.328l-3.32 3.319" />
                                    </svg>
                                    <h4 class="text-lg font-bold text-gray-800">Status Tes Penempatan</h4>
                                </div>
                                @if ($placementTest)
                                    <p class="text-sm text-gray-700 mt-2">Level Anda: <span class="font-medium text-brand-teal">{{ $placementTest->finalLevel->name ?? 'Belum Ditetapkan' }}</span></p>
                                    <p class="text-sm text-gray-700">Skor Audio: {{ $placementTest->audio_reading_score ?? 'Belum Dinilai' }}</p>
                                    <p class="text-sm text-gray-700">Skor Teori: {{ $placementTest->theory_score ?? 'N/A' }}</p>
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('mentee.report.index') }}" class="text-brand-teal hover:underline text-sm">Lihat Laporan Lengkap</a>
                                    </div>
                                @else
                                    <p class="text-gray-600">Anda belum mengikuti tes penempatan.</p>
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('placement-test.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-brand-teal hover:bg-brand-gold focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2">
                                            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 100 15 7.5 7.5 0 000-15z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.25-5.25" />
                                            </svg>
                                            Ikuti Tes Sekarang
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Card Kelompok Mentoring --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
                            <div>
                                <div class="flex items-center mb-3">
                                    <svg class="h-6 w-6 text-brand-teal mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962A3 3 0 0115 9.185a3 3 0 01-4.5 2.72m-7.5-2.962a3 3 0 00-4.682 2.72 8.982 8.982 0 003.741.479M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h4 class="text-lg font-bold text-gray-800">Kelompok Mentoring Saya</h4>
                                </div>
                                @if ($mentoringGroup)
                                    <p class="text-sm text-gray-700 mt-2">Nama Kelompok: <span class="font-medium text-brand-teal">{{ $mentoringGroup->name }}</span></p>
                                    <p class="text-sm text-gray-700">Mentor: <span class="font-medium">{{ $mentoringGroup->mentor->name ?? 'N/A' }}</span></p>
                                    <p class="text-sm text-gray-700">Level: <span class="font-medium">{{ $mentoringGroup->level->name ?? 'N/A' }}</span></p>
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('mentee.group.index') }}" class="text-brand-teal hover:underline text-sm">Lihat Detail Kelompok</a>
                                    </div>
                                @else
                                    <p class="text-gray-600">Anda belum ditugaskan ke kelompok mentoring.</p>
                                    <p class="text-sm text-gray-500">Silakan hubungi administrator.</p>
                                @endif
                            </div>
                        </div>

                        {{-- Card Ringkasan Progres --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
                            <div>
                                <div class="flex items-center mb-3">
                                    <svg class="h-6 w-6 text-brand-teal mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5l6-6 4 4 6-6M3 13.5h18" />
                                    </svg>
                                    <h4 class="text-lg font-bold text-gray-800">Ringkasan Progres</h4>
                                </div>
                                @if ($mentoringGroup)
                                    <p class="text-sm text-gray-700 mt-2">Total Sesi: <span class="font-medium">{{ $totalSessions }}</span></p>
                                    <p class="text-sm text-gray-700">Sesi Dihadiri: <span class="font-medium">{{ $attendedSessions }}</span></p>
                                    <p class="text-sm text-gray-700">Rata-rata Skor: <span class="font-medium">{{ $averageScore }}</span></p>
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('mentee.sessions.index') }}" class="text-brand-teal hover:underline text-sm">Lihat Detail Sesi</a>
                                    </div>
                                @else
                                    <p class="text-gray-600">Belum ada data progres mentoring.</p>
                                    <p class="text-sm text-gray-500">Anda belum ditugaskan ke kelompok.</p>
                                @endif
                            </div>
                        </div>

                        {{-- Card Pengumuman Terbaru --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-200 md:col-span-2 lg:col-span-1">
                            <div>
                                <div class="flex items-center mb-3">
                                    <svg class="h-6 w-6 text-brand-teal mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688 0-1.25-.562-1.25-1.25s.562-1.25 1.25-1.25h3.32c.688 0 1.25.562 1.25 1.25s-.562 1.25-1.25 1.25h-3.32zM9 19.5h6" />
                                    </svg>
                                    <h4 class="text-lg font-bold text-gray-800">Pengumuman Terbaru</h4>
                                </div>
                                @forelse ($latestAnnouncements as $announcement)
                                    <div class="mt-2 text-sm">
                                        <p class="font-medium text-gray-700">{{ $announcement->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $announcement->published_at->format('d M Y') }}</p>
                                    </div>
                                @empty
                                    <p class="text-gray-600">Belum ada pengumuman terbaru.</p>
                                @endforelse
                                <div class="mt-4 text-center">
                                    <a href="{{ route('mentee.announcements.index') }}" class="text-brand-teal hover:underline text-sm">Lihat Semua Pengumuman</a>
                                </div>
                            </div>
                        </div>

                        {{-- Card Sesi Mendatang --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-200">
                            <div>
                                <div class="flex items-center mb-3">
                                    <svg class="h-6 w-6 text-brand-teal mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                    <h4 class="text-lg font-bold text-gray-800">Sesi Mendatang</h4>
                                </div>
                                @forelse ($upcomingSessions as $session)
                                    <div class="mt-2 text-sm">
                                        <p class="font-medium text-gray-700">{{ $session->topic }}</p>
                                        <p class="text-xs text-gray-500">Tanggal: {{ $session->date->format('d M Y') }}</p>
                                    </div>
                                @empty
                                    <p class="text-gray-600">Tidak ada sesi mendatang yang terjadwal.</p>
                                @endforelse
                                <div class="mt-4 text-center">
                                    <a href="{{ route('mentee.sessions.index') }}" class="text-brand-teal hover:underline text-sm">Lihat Semua Sesi</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bagian Akses Cepat --}}
                    <div class="mt-10 p-6 bg-brand-teal/10 rounded-lg shadow-sm">
                        <h4 class="text-xl font-bold text-brand-ink mb-4">Akses Cepat</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <a href="{{ route('mentee.materials.index') }}" class="inline-flex items-center justify-center px-4 py-3 border border-brand-teal text-base font-medium rounded-md shadow-sm text-brand-teal bg-white hover:bg-brand-teal hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2">
                                <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                                Materi Belajar
                            </a>
                            <a href="{{ route('mentee.report.index') }}" class="inline-flex items-center justify-center px-4 py-3 border border-brand-teal text-base font-medium rounded-md shadow-sm text-brand-teal bg-white hover:bg-brand-teal hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2">
                                <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12H12m-2.25 4.5H12M12 18.75V15m-1.5 2.25l-1.5-1.5m1.5 1.5l1.5-1.5M12 18.75L10.5 17.25M12 18.75L13.5 17.25M12 14.25h-2.25M15 11.25H9M15 12h-2.25" />
                                </svg>
                                Laporan Hasil Mentoring
                            </a>
                            <a href="{{ route('mentee.group.index') }}" class="inline-flex items-center justify-center px-4 py-3 border border-brand-teal text-base font-medium rounded-md shadow-sm text-brand-teal bg-white hover:bg-brand-teal hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2">
                                <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962A3 3 0 0115 9.185a3 3 0 01-4.5 2.72m-7.5-2.962a3 3 0 00-4.682 2.72 8.982 8.982 0 003.741.479M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Kelompok Mentoring
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
