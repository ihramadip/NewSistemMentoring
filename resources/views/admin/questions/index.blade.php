<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Kelola Pertanyaan Ujian') }}" subtitle="Tambahkan, edit, atau hapus pertanyaan untuk ujian: {{ $exam->name }}">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712L12 16.125l-2.121-2.121c-1.172-1.025-1.172-2.687 0-3.712z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 12.75l1.5 1.5M12 12.75l-1.5 1.5M12 12.75V7.5" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-medium text-gray-900 mb-4">Daftar Pertanyaan</h3>
                    
                    @if(session('success'))
                        <div class="bg-teal-100 border border-teal-400 text-teal-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-end mb-4">
                        <a href="{{ route('admin.exams.questions.create', $exam) }}" class="inline-flex items-center px-4 py-2 bg-brand-teal border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-gold active:bg-brand-gold focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah Pertanyaan
                        </a>
                    </div>

                    <div class="space-y-6">
                        @forelse ($questions as $question)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-lg font-bold text-gray-800">{{ $loop->iteration }}. {{ $question->question_text }}</h4>
                                        <div class="flex items-center space-x-2">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ $question->score_value }} Poin</span>
                                        </div>
                                    </div>
                                    
                                    @if($question->question_type === 'multiple_choice' && $question->options->isNotEmpty())
                                        <ul class="mt-4 space-y-2 text-sm text-gray-700">
                                            @foreach($question->options as $option)
                                                <li class="flex items-start">
                                                    <span class="mr-2 {{ $option->is_correct ? 'text-green-600' : 'text-gray-500' }}">
                                                        @if($option->is_correct)
                                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        @else
                                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        @endif
                                                    </span>
                                                    {{ $option->option_text }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                                <div class="mt-4 flex justify-end items-center space-x-3">
                                    <a href="{{ route('admin.exams.questions.edit', [$exam, $question]) }}" class="text-sm text-brand-teal hover:text-brand-gold">Edit</a>
                                    <form action="{{ route('admin.exams.questions.destroy', [$exam, $question]) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800" onclick="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712L12 16.125l-2.121-2.121c-1.172-1.025-1.172-2.687 0-3.712z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 12.75l1.5 1.5M12 12.75l-1.5 1.5M12 12.75V7.5" />
                                </svg>
                                <p class="mt-4 text-sm text-gray-500">Belum ada pertanyaan untuk ujian ini.</p>
                                <p class="mt-2 text-sm text-gray-500">Klik "Tambah Pertanyaan" untuk memulai.</p>
                            </div>
                        @endforelse
                    </div>

                    @if ($questions instanceof \Illuminate\Pagination\AbstractPaginator)
                        <div class="mt-4">
                            {{ $questions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
