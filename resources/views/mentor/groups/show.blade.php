<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ $group->name }}" subtitle="Detail kelompok, anggota, dan daftar sesi mentoring.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.028.658a.79.79 0 01-.588.792l-.028.008a.786.786 0 01-.766-.36zM14 8a2 2 0 11-4 0 2 2 0 014 0zM10 18a6.484 6.484 0 005.065-2.915 3 3 0 01-4.308-3.516.78.78 0 01.358-.442l.028.008a.79.79 0 01.588.792.786.786 0 01-.766.36 4.486 4.486 0 01-1.905 3.959c-.023.222-.014.442.028.658a.79.79 0 01-.588.792l-.028.008a.786.786 0 01-.766-.36z" />
                </svg>
            </x-slot>
            <x-slot name="actions">
                <a href="{{ route('mentor.groups.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                    </svg>
                    Kembali
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Main Content (Sessions) -->
                <div class="lg:col-span-2 space-y-6">
                    @forelse($group->sessions as $session)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-brand-teal">PERTEMUAN KE-{{ $loop->iteration }}</p>
                                        <h3 class="text-lg font-bold text-gray-800 mt-1">{{ $session->title }}</h3>
                                        <p class="text-sm text-gray-500 mt-1">{{ $session->date->format('l, d F Y') }}</p>
                                    </div>
                                    <div class="mt-4 sm:mt-0">
                                        <a href="{{ route('mentor.sessions.show', $session) }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-brand-ink border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-ink/90 focus:outline-none focus:ring-2 focus:ring-brand-ink focus:ring-offset-2 transition ease-in-out duration-150">
                                            Kelola Absensi & Laporan
                                        </a>
                                    </div>
                                </div>
                                <div class="mt-4 pt-4 border-t">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-semibold">Kehadiran:</span>
                                        {{ $session->attendances->where('status', 'present')->count() }} / {{ $group->members->count() }} Mentee Hadir
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                  </svg>
                                <h3 class="mt-2 text-lg font-medium">Belum ada sesi</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    Jadwal sesi untuk kelompok ini belum ditambahkan oleh admin.
                                </p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Right Sidebar (Members) -->
                <div class="space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-base font-semibold text-gray-900">
                                Anggota Kelompok ({{ $group->members->count() }})
                            </h3>
                            <ul class="mt-4 space-y-3">
                                @forelse($group->members as $member)
                                    <li class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                            {{ strtoupper(substr($member->name, 0, 2)) }}
                                        </div>
                                        <span class="text-sm text-gray-700">{{ $member->name }}</span>
                                    </li>
                                @empty
                                    <li class="text-sm text-gray-500">Belum ada anggota di kelompok ini.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
