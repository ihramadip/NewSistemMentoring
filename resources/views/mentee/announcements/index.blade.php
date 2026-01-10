<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Pengumuman') }}" subtitle="Informasi penting dan pengumuman terbaru dari program mentoring.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688 0-1.25-.562-1.25-1.25s.562-1.25 1.25-1.25h3.32c.688 0 1.25.562 1.25 1.25s-.562 1.25-1.25 1.25h-3.32zM9 19.5h6" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Pengumuman Terbaru</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                        @forelse ($announcements as $announcement)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
                                <div>
                                    <div class="flex items-center mb-3">
                                        <svg class="h-6 w-6 text-brand-teal mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688 0-1.25-.562-1.25-1.25s.562-1.25 1.25-1.25h3.32c.688 0 1.25.562 1.25 1.25s-.562 1.25-1.25 1.25h-3.32zM9 19.5h6" />
                                        </svg>
                                        <h4 class="text-lg font-bold text-gray-800">{{ $announcement->title }}</h4>
                                    </div>
                                    <p class="text-sm text-gray-700 mb-4">{{ $announcement->content }}</p>
                                    <p class="text-xs text-gray-500 mb-2">Diterbitkan pada: {{ $announcement->published_at->format('d M Y H:i') }}</p>
                                </div>
                                @if($announcement->file_path)
                                    <div class="mt-4">
                                        <a href="{{ Storage::url($announcement->file_path) }}" target="_blank" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-brand-teal hover:bg-brand-gold focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2">
                                            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                            </svg>
                                            Unduh Lampiran
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="col-span-full bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688 0-1.25-.562-1.25-1.25s.562-1.25 1.25-1.25h3.32c.688 0 1.25.562 1.25 1.25s-.562 1.25-1.25 1.25h-3.32zM9 19.5h6" />
                                </svg>
                                <p class="mt-4 text-sm text-gray-500">Belum ada pengumuman terbaru saat ini.</p>
                                <p class="mt-2 text-sm text-gray-500">Mohon periksa kembali nanti atau hubungi administrator.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
