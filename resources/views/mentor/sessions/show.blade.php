<x-app-layout>
    <x-slot name="header">
        <x-page-header :title="'Sesi ke-'.$session->session_number.': '.$session->title" :subtitle="$session->mentoringGroup->name.' - '.$session->date->format('l, d M Y')">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v1.755S5.47 5.1 7 5.914V4a1 1 0 112 0v1.914S10.47 5.1 12 5.914V4a1 1 0 112 0v1.914S15.47 5.1 17 5.914V4a1 1 0 112 0v2.414c0 .265-.105.52-.293.707l-2.414 2.414-2.415-2.414a1 1 0 01-.292-.707V4a1 1 0 10-2 0v1.914S10.47 5.1 12 5.914V4a1 1 0 10-2 0v1.914S8.47 5.1 7 5.914V4a1 1 0 00-2 0v1.914S3.47 5.1 2 5.914V3a1 1 0 011-1zm0 8a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zm0 4a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1z" clip-rule="evenodd" />
                </svg>
            </x-slot>
            <x-slot name="actions">
                <a href="{{ route('mentor.groups.show', $session->mentoringGroup) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                    </svg>
                    Kembali ke Detail Kelompok
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('mentor.sessions.update', $session) }}">
                @csrf
                @method('PATCH')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-800 border-b pb-4 mb-4">Form Absensi & Laporan Progres</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mentee</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kehadiran</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Skor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">Catatan Bacaan/Progres</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($session->mentoringGroup->members as $member)
                                        <tr class="@if($loop->even) bg-gray-50 @endif border-b">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $member->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @php $currentStatus = $attendances[$member->id]->status ?? null; @endphp
                                                <x-select-input name="attendances[{{ $member->id }}][status]" class="w-full">
                                                    <option value="present" @selected($currentStatus == 'present')>Hadir</option>
                                                    <option value="absent" @selected($currentStatus == 'absent')>Absen</option>
                                                    <option value="excused" @selected($currentStatus == 'excused')>Izin</option>
                                                </x-select-input>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <x-text-input type="number" name="reports[{{ $member->id }}][score]"
                                                              value="{{ $progressReports[$member->id]->score ?? '' }}"
                                                              class="w-24 text-center" min="0" max="100" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <x-textarea-input name="reports[{{ $member->id }}][reading_notes]"
                                                                  class="w-full"
                                                                  rows="2">{{ $progressReports[$member->id]->reading_notes ?? '' }}</x-textarea-input>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <x-primary-button>
                        {{ __('Simpan Absensi & Laporan') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
