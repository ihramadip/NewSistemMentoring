<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Daftar Ujian') }}" subtitle="Lihat dan ikuti ujian yang tersedia sesuai level Anda.">
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
                    @if(session('success'))
                        <div class="bg-teal-100 border border-teal-400 text-teal-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('warning'))
                        <div class="bg-orange-100 border border-orange-400 text-orange-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('warning') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Ujian yang Tersedia</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($availableExams as $exam)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
                                <div>
                                    <h4 class="text-lg font-bold text-gray-800 mb-2">{{ $exam->name }}</h4>
                                    <p class="text-sm text-gray-700 mb-2">{{ $exam->description }}</p>
                                    <p class="text-xs text-gray-600">Level: {{ $exam->level->name ?? 'Semua Level' }}</p>
                                    <p class="text-xs text-gray-600">Durasi: {{ $exam->duration_minutes ?? 'Tidak Terbatas' }} menit</p>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('mentee.exams.show', $exam) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-brand-teal hover:bg-brand-gold focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2">
                                        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l3 3m0 0l3-3m-3-3v6m-4.5 5.25H9.75a2.25 2.25 0 002.25-2.25V7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Mulai Ujian
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12H12m-2.25 4.5H12M12 18.75V15m-1.5 2.25l-1.5-1.5m1.5 1.5l1.5-1.5M12 18.75L10.5 17.25M12 18.75L13.5 17.25M12 14.25h-2.25M15 11.25H9M15 12h-2.25" />
                                </svg>
                                <p class="mt-4 text-sm text-gray-500">Belum ada ujian yang tersedia untuk Anda saat ini.</p>
                                <p class="mt-2 text-sm text-gray-500">Silakan periksa kembali nanti atau hubungi administrator.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6 border-t pt-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Ujian yang Telah Anda Selesaikan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse ($completedExams as $exam)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
                                    <div>
                                        <h4 class="text-lg font-bold text-gray-800 mb-2">{{ $exam->name }}</h4>
                                        <p class="text-sm text-gray-700 mb-2">{{ $exam->description }}</p>
                                        <p class="text-xs text-gray-600">Level: {{ $exam->level->name ?? 'Semua Level' }}</p>
                                        <p class="text-xs text-gray-600 text-green-600">Status: Sudah Selesai</p>
                                    </div>
                                    <div class="mt-4">
                                        {{-- Link to view results or details of completed exam --}}
                                        <a href="#" class="inline-flex items-center px-4 py-2 border border-brand-teal text-sm font-medium rounded-md shadow-sm text-brand-teal bg-white hover:bg-brand-teal hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2">
                                            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Lihat Hasil
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center text-gray-600">Belum ada ujian yang Anda selesaikan.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
