<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Progres Mentee') }}" subtitle="Rekapitulasi progres dan kehadiran semua mentee yang Anda bimbing.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.75 2.75a.75.75 0 00-1.5 0v8.5a.75.75 0 001.5 0v-8.5zM10 20a9.5 9.5 0 100-19 9.5 9.5 0 000 19zM10 1.5a8 8 0 110 16 8 8 0 010-16z" />
                    <path d="M13.28 5.72a.75.75 0 00-1.06-1.06l-4.5 4.5a.75.75 0 000 1.06l4.5 4.5a.75.75 0 101.06-1.06L9.28 10.5l4-4.78z" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="border-b">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mentee</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelompok</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tingkat Kehadiran</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rata-rata Skor</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mentees as $mentee)
                                    <tr class="@if($loop->even) bg-gray-50 @endif border-b">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $mentee->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $mentee->mentoringGroupsAsMentee->first()->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="font-semibold">{{ $mentee->attendance_rate }}%</span>
                                            <span class="text-gray-500"> ({{ $mentee->attendance_summary }})</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $mentee->average_score >= 75 ? 'text-green-600' : 'text-yellow-600' }}">
                                            {{ $mentee->average_score }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{-- route('mentor.reports.show', $mentee) --}}" class="text-indigo-600 hover:text-indigo-900">Lihat Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Belum ada data mentee yang bisa ditampilkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
