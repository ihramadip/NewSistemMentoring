<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            <x-slot name="icon">
                <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot>
            <x-slot name="title">
                Beri Nilai Ujian Akhir
            </x-slot>
            <x-slot name="subtitle">
                Review jawaban untuk {{ $submission->mentee->name }} pada ujian "{{ $submission->exam->title }}"
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    
                    <!-- Submission Details -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Detail Jawaban</h3>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Nama Mentee</p>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $submission->mentee->name }} ({{ $submission->mentee->npm }})</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Waktu Submit</p>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $submission->submitted_at->format('d M Y, H:i:s') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Answers -->
                    <div class="space-y-6 mb-8">
                        @foreach($submission->answers as $index => $answer)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">Pertanyaan {{ $index + 1 }}</p>
                                <p class="mt-1 text-base text-gray-900 dark:text-white font-medium">{!! nl2br(e($answer->question->question_text)) !!}</p>
                                
                                <div class="mt-4">
                                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">Jawaban Mentee:</p>
                                    @if($answer->question->type === 'multiple_choice')
                                        @if($answer->option)
                                            <p class="text-base p-3 mt-1 rounded-lg {{ $answer->option->is_correct ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $answer->option->option_text }}
                                                @if($answer->option->is_correct)
                                                    <span class="font-semibold ml-2">(Benar)</span>
                                                @else
                                                    <span class="font-semibold ml-2">(Salah)</span>
                                                @endif
                                            </p>
                                        @else
                                            <p class="text-base p-3 mt-1 rounded-lg bg-yellow-100 text-yellow-800">Tidak dijawab</p>
                                        @endif
                                    @else
                                        <p class="text-base p-3 mt-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">{!! nl2br(e($answer->answer_text ?? 'Tidak dijawab')) !!}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Grading Form -->
                    <form action="{{ route('admin.final-exam-grading.update', $submission) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                             <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Form Penilaian</h3>
                            <div>
                                <x-input-label for="total_score" value="Nilai Akhir (0-100)" />
                                <x-text-input id="total_score" name="total_score" type="number" class="mt-1 block w-full md:w-1/3" value="{{ old('total_score', $submission->total_score) }}" required />
                                <x-input-error :messages="$errors->get('total_score')" class="mt-2" />
                            </div>

                            <div class="mt-6 flex items-center gap-4">
                                <x-primary-button>Simpan Nilai</x-primary-button>
                                <a href="{{ route('admin.final-exam-grading.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Batal</a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
