<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Sesi Mentoring Saya') }}" subtitle="Lihat jadwal, status absensi, dan laporan progres Anda di setiap sesi mentoring.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Sesi untuk Kelompok: <span class="text-brand-teal">{{ $mentoringGroup->name ?? 'N/A' }}</span></h3>

                    @if(session('warning'))
                        <div class="bg-orange-100 border border-orange-400 text-orange-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('warning') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($sessions as $session)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
                                <div>
                                    <div class="flex items-center mb-3">
                                        <svg class="h-6 w-6 text-brand-teal mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                        </svg>
                                        <h4 class="text-lg font-bold text-gray-800">Sesi #{{ $session->session_number }}: {{ $session->topic }}</h4>
                                    </div>
                                    <p class="text-sm text-gray-700 mb-4">Tanggal: <span class="font-medium">{{ $session->date->format('d M Y') }}</span></p>
                                    
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <p class="text-md font-semibold text-gray-800 mb-2">Absensi Anda:</p>
                                        @php
                                            $attendance = $session->attendances->first();
                                        @endphp
                                        @if ($attendance)
                                            <p class="text-sm text-gray-700 ml-2">Status: <span class="font-bold text-brand-teal">{{ ucfirst($attendance->status) }}</span></p>
                                            @if($attendance->notes)
                                                <p class="text-sm text-gray-700 ml-2">Catatan: {{ $attendance->notes }}</p>
                                            @endif
                                        @else
                                            <p class="text-sm text-gray-600 ml-2">Belum ada data absensi untuk sesi ini.</p>
                                        @endif
                                    </div>

                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <p class="text-md font-semibold text-gray-800 mb-2">Laporan Progres Anda:</p>
                                        @php
                                            $progressReport = $session->progressReports->first();
                                        @endphp
                                        @if ($progressReport)
                                            <p class="text-sm text-gray-700 ml-2">Skor: <span class="font-bold text-brand-teal">{{ $progressReport->score ?? 'N/A' }}</span></p>
                                            @if($progressReport->reading_notes)
                                                <p class="text-sm text-gray-700 ml-2">Catatan Bacaan: {{ $progressReport->reading_notes }}</p>
                                            @endif
                                            @if($progressReport->general_notes)
                                                <p class="text-sm text-gray-700 ml-2">Catatan Umum: {{ $progressReport->general_notes }}</p>
                                            @endif
                                        @else
                                            <p class="text-sm text-gray-600 ml-2">Belum ada laporan progres untuk sesi ini.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                                <p class="mt-4 text-sm text-gray-500">Belum ada sesi mentoring yang tercatat untuk kelompok Anda.</p>
                                <p class="mt-2 text-sm text-gray-500">Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
