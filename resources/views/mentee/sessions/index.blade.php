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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">
            <!-- Mandatory Sessions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">Sesi Wajib (oleh Mentor)</h3>
                    
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
                                <p class="mt-4 text-sm text-gray-500">Belum ada sesi mentoring wajib yang tercatat untuk kelompok Anda.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Additional Sessions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6 border-b pb-4">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">Sesi Tambahan (Mandiri)</h3>
                            <p class="text-sm text-gray-500 mt-1">Anda dapat menambahkan hingga 21 sesi tambahan secara mandiri.</p>
                        </div>
                            <a href="{{ route('additional-sessions.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-teal border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:brightness-90 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Tambah Sesi
                            </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Topik</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bukti</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($additionalSessions as $session)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $session->topic }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($session->date)->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($session->status == 'sudah')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Sudah
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Belum
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($session->proof_path)
                                                <a href="{{ Storage::url($session->proof_path) }}" target="_blank" class="text-brand-teal hover:underline">Lihat Bukti</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('additional-sessions.edit', $session) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <span class="mx-1 text-gray-300">|</span>
                                            <form action="{{ route('additional-sessions.destroy', $session) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sesi ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Belum ada sesi tambahan yang Anda tambahkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-sm text-gray-600">
                        Menampilkan {{ $additionalSessions->count() }} dari 21 sesi tambahan.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>