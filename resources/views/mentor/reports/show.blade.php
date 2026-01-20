<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Detail Progres Mentee: {{ $mentee->name }}">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
            </x-slot>
            <x-slot name="action">
                <a href="{{ route('mentor.reports.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Kembali
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Kolom Statistik Ringkas -->
                <div class="md:col-span-1 space-y-6">
                    <!-- Card Informasi Mentee -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $mentee->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $mentee->email }}</p>
                            <p class="mt-2 text-sm text-gray-600">Kelompok: <span class="font-medium">{{ $mentee->mentoringGroupsAsMentee->first()->name ?? 'N/A' }}</span></p>
                        </div>
                    </div>
                    <!-- Card Statistik Kehadiran -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800">Statistik Kehadiran</h3>
                            <p class="mt-1 text-3xl font-bold text-indigo-600">{{ $attendance_rate }}%</p>
                            <p class="text-sm text-gray-500">Total Kehadiran: {{ $present_count }} dari {{ $total_sessions }} sesi</p>
                        </div>
                    </div>
                    <!-- Card Rata-rata Skor -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800">Rata-rata Skor</h3>
                            <p class="mt-1 text-3xl font-bold {{ $average_score >= 75 ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $average_score }}
                            </p>
                            <p class="text-sm text-gray-500">Berdasarkan {{ $reports_count }} laporan progres</p>
                        </div>
                    </div>
                </div>

                <!-- Kolom Detail Progres -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Tabel Detail Kehadiran -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Rincian Kehadiran per Sesi</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Sesi</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Sesi</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($sessions as $session)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->date->format('d M Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $session->title }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    @php
                                                        $attendance = $attendances->firstWhere('session_id', $session->id);
                                                        $status = $attendance->status ?? 'absen';
                                                        $badgeColor = [
                                                            'hadir' => 'bg-green-100 text-green-800',
                                                            'izin' => 'bg-yellow-100 text-yellow-800',
                                                            'absen' => 'bg-red-100 text-red-800',
                                                        ][$status];
                                                    @endphp
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }}">
                                                        {{ ucfirst($status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data sesi.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Tabel Laporan Progres -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Rincian Laporan Progres</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sesi</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Skor</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($progress_reports as $report)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $report->session->title ?? 'N/A' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $report->score >= 75 ? 'text-green-600' : 'text-yellow-600' }}">{{ $report->score }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-800">{{ $report->notes }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada laporan progres yang dibuat.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
