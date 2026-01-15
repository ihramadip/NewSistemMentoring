<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Manajemen Ujian') }}" subtitle="Kelola daftar ujian, pertanyaan, dan level terkait.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12H12m-2.25 4.5H12M12 18.75V15m-1.5 2.25l-1.5-1.5m1.5 1.5l1.5-1.5M12 18.75L10.5 17.25M12 18.75L13.5 17.25M12 14.25h-2.25M15 11.25H9M15 12h-2.25" />
                </svg>
            </x-slot>
            <x-slot name="actions">
                <x-primary-button href="{{ route('admin.exams.create') }}">
                    {{ __('Tambah Ujian') }}
                </x-primary-button>
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

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Ujian</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi (Menit)</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Publikasi</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($exams as $exam)
                                    <tr class="@if($loop->even) bg-brand-mist/20 @endif hover:bg-brand-mist/40">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $exam->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $exam->level->name ?? 'Semua Level' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $exam->duration_minutes ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            @if ($exam->published_at)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Dipublikasikan ({{ $exam->published_at->format('d M Y') }})
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.exams.show', $exam) }}" class="text-blue-600 hover:text-blue-900 mr-3">Detail</a>
                                            <a href="{{ route('admin.exams.edit', $exam) }}" class="text-brand-teal hover:text-brand-gold mr-3">Edit</a>
                                            <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus ujian ini?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Tidak ada ujian yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $exams->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
