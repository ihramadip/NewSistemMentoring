<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Laporan Hasil Mentoring') }}" subtitle="Rangkuman lengkap perjalanan mentoring Anda, termasuk hasil tes dan progres sesi.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12H12m-2.25 4.5H12M12 18.75V15m-1.5 2.25l-1.5-1.5m1.5 1.5l1.5-1.5M12 18.75L10.5 17.25M12 18.75L13.5 17.25M12 14.25h-2.25M15 11.25H9M15 12h-2.25" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Laporan Mentoring untuk <span class="text-brand-teal">{{ $user->name }} ({{ $user->npm }})</span></h3>

                    @if(session('warning'))
                        <div class="bg-orange-100 border border-orange-400 text-orange-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('warning') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                        <!-- Placement Test Summary -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
                            <div>
                                <div class="flex items-center mb-3">
                                    <svg class="h-6 w-6 text-brand-teal mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 100 15 7.5 7.5 0 000-15z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.25-5.25" />
                                    </svg>
                                    <h4 class="text-lg font-bold text-gray-800">Ringkasan Tes Penempatan</h4>
                                </div>
                                @if ($placementTest)
                                    <p class="text-sm text-gray-700 mt-2">Skor Audio Bacaan: <span class="font-medium">{{ $placementTest->audio_reading_score ?? 'Belum dinilai' }}</span></p>
                                    <p class="text-sm text-gray-700">Skor Teori: <span class="font-medium">{{ $placementTest->theory_score ?? 'N/A' }}</span></p>
                                    <p class="text-sm text-gray-700">Level Akhir: <span class="font-medium text-brand-teal">{{ $placementTest->finalLevel->name ?? 'Belum ditetapkan' }}</span></p>
                                @else
                                    <div class="mt-4 text-center">
                                        <p class="text-gray-600">Anda belum mengikuti tes penempatan.</p>
                                        <a href="{{ route('placement-test.create') }}" class="text-brand-teal hover:underline text-sm mt-2 block">Ikuti Tes Penempatan Sekarang</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Mentoring Group Summary -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
                            <div>
                                <div class="flex items-center mb-3">
                                    <svg class="h-6 w-6 text-brand-teal mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962A3 3 0 0115 9.185a3 3 0 01-4.5 2.72m-7.5-2.962a3 3 0 00-4.682 2.72 8.982 8.982 0 003.741.479M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h4 class="text-lg font-bold text-gray-800">Informasi Kelompok Mentoring</h4>
                                </div>
                                @if ($mentoringGroup)
                                    <p class="text-sm text-gray-700 mt-2">Nama Kelompok: <span class="font-medium">{{ $mentoringGroup->name }}</span></p>
                                    <p class="text-sm text-gray-700">Mentor: <span class="font-medium">{{ $mentoringGroup->mentor->name ?? 'N/A' }}</span></p>
                                    <p class="text-sm text-gray-700">Level Kelompok: <span class="font-medium">{{ $mentoringGroup->level->name ?? 'N/A' }}</span></p>
                                    <p class="text-sm text-gray-700">Jadwal: <span class="font-medium">{{ $mentoringGroup->schedule_info ?? 'Belum ditentukan' }}</span></p>
                                @else
                                    <div class="mt-4 text-center">
                                        <p class="text-gray-600">Anda belum ditugaskan ke kelompok mentoring.</p>
                                        <a href="{{ route('mentee.group.index') }}" class="text-brand-teal hover:underline text-sm mt-2 block">Lihat Kelompok Saya</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Overall Session Summary -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
                            <div>
                                <div class="flex items-center mb-3">
                                    <svg class="h-6 w-6 text-brand-teal mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5l6-6 4 4 6-6M3 13.5h18" />
                                    </svg>
                                    <h4 class="text-lg font-bold text-gray-800">Ringkasan Sesi Keseluruhan</h4>
                                </div>
                                @if ($mentoringGroup && $totalSessions > 0)
                                    <p class="text-sm text-gray-700 mt-2">Total Sesi: <span class="font-medium">{{ $totalSessions }}</span></p>
                                    <p class="text-sm text-gray-700">Sesi Dihadiri: <span class="font-medium">{{ $attendedSessions }}</span></p>
                                    <p class="text-sm text-gray-700">Rata-rata Skor Progres: <span class="font-medium">{{ number_format($averageScore, 2) ?? 'N/A' }}</span></p>
                                @else
                                    <div class="mt-4 text-center">
                                        <p class="text-gray-600">Belum ada data sesi mentoring.</p>
                                        <a href="{{ route('mentee.sessions.index') }}" class="text-brand-teal hover:underline text-sm mt-2 block">Lihat Sesi Saya</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Detailed Session Data (Optional, can be a link to sessions page) -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
                            <div>
                                <div class="flex items-center mb-3">
                                    <svg class="h-6 w-6 text-brand-teal mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12H12m-2.25 4.5H12M12 18.75V15m-1.5 2.25l-1.5-1.5m1.5 1.5l1.5-1.5M12 18.75L10.5 17.25M12 18.75L13.5 17.25M12 14.25h-2.25M15 11.25H9M15 12h-2.25" />
                                    </svg>
                                    <h4 class="text-lg font-bold text-gray-800">Detail Sesi</h4>
                                </div>
                                @if ($sessionsData->isNotEmpty())
                                    <div class="mt-4 text-center">
                                        <p class="text-gray-600">Lihat detail absensi dan laporan progres untuk setiap sesi di halaman:</p>
                                        <a href="{{ route('mentee.sessions.index') }}" class="inline-flex items-center justify-center px-4 py-2 mt-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-brand-teal hover:bg-brand-gold focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2">
                                            <svg class class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                            </svg>
                                            Sesi Mentoring Saya
                                        </a>
                                    </div>
                                @else
                                    <div class="mt-4 text-center">
                                        <p class="text-gray-600">Tidak ada sesi mentoring yang tercatat.</p>
                                        <p class="text-sm text-gray-500">Silakan periksa kembali nanti atau hubungi administrator.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
