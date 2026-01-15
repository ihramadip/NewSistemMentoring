<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Detail Ujian') }}">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12H12m-2.25 4.5H12M12 18.75V15m-1.5 2.25l-1.5-1.5m1.5 1.5l1.5-1.5M12 18.75L10.5 17.25M12 18.75L13.5 17.25M12 14.25h-2.25M15 11.25H9M15 12h-2.25" />
                </svg>
            </x-slot>
            <x-slot name="actions">
                <a href="{{ route('admin.exams.edit', $exam) }}" class="inline-flex items-center px-4 py-2 bg-brand-teal border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-gold active:bg-brand-gold focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Edit Ujian') }}
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Exam Details Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">{{ $exam->name }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $exam->description ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Level</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $exam->level->name ?? 'Semua Level' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Durasi</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $exam->duration_minutes ? $exam->duration_minutes . ' menit' : 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status Publikasi</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if ($exam->published_at)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Dipublikasikan ({{ $exam->published_at->format('d M Y, H:i') }})
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Draft
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dibuat oleh</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $exam->creator->name ?? 'N/A' }}</dd>
                        </div>
                         <div>
                            <dt class="text-sm font-medium text-gray-500">Jumlah Pertanyaan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $exam->questions->count() }}</dd>
                        </div>
                    </div>
                </div>
                 <div class="p-6 bg-gray-50/50">
                    <a href="{{ route('admin.exams.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Kembali ke Daftar Ujian') }}
                    </a>
                </div>
            </div>

            <!-- Question List Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-8">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-xl font-bold text-gray-800">Daftar Pertanyaan</h3>
                        <a href="{{ route('admin.exams.questions.create', $exam) }}" class="inline-flex items-center px-4 py-2 bg-brand-teal border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-gold active:bg-brand-gold focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah Pertanyaan
                        </a>
                    </div>
                    
                    @include('admin.questions._index-partial', ['exam' => $exam, 'questions' => $exam->questions])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>