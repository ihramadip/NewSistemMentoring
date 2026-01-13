<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Sesi & Laporan') }}" subtitle="Daftar semua sesi mentoring dari kelompok bimbingan Anda.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v1.755S5.47 5.1 7 5.914V4a1 1 0 112 0v1.914S10.47 5.1 12 5.914V4a1 1 0 112 0v1.914S15.47 5.1 17 5.914V4a1 1 0 112 0v2.414c0 .265-.105.52-.293.707l-2.414 2.414-2.415-2.414a1 1 0 01-.292-.707V4a1 1 0 10-2 0v1.914S10.47 5.1 12 5.914V4a1 1 0 10-2 0v1.914S8.47 5.1 7 5.914V4a1 1 0 00-2 0v1.914S3.47 5.1 2 5.914V3a1 1 0 011-1zm0 8a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zm0 4a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1z" clip-rule="evenodd" />
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
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Sesi</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Topik Sesi</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelompok</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kehadiran</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sessions as $session)
                                    <tr class="@if($loop->even) bg-brand-mist @endif border-b border-gray-200 hover:bg-brand-sky/40">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $session->date->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $session->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $session->mentoringGroup->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{-- Placeholder for attendance summary --}}
                                            - / -
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('mentor.sessions.show', $session) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Kelola</a>
                                            <form action="{{ route('mentor.sessions.destroy', $session) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus sesi ini? Semua data absensi dan laporan terkait akan ikut terhapus.')">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Belum ada data sesi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $sessions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
