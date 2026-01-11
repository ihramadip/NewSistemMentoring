<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Ujian: ' . $exam->name) }}" subtitle="Jawab semua pertanyaan dengan seksama.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12H12m-2.25 4.5H12M12 18.75V15m-1.5 2.25l-1.5-1.5m1.5 1.5l1.5-1.5M12 18.75L10.5 17.25M12 18.75L13.5 17.25M12 14.25h-2.25M15 11.25H9M15 12h-2.25" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div x-data="timer({{ $exam->duration_minutes ?? 0 }})" x-init="startTimer()" class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Timer bar -->
            <div class="fixed top-16 right-0 left-0 lg:left-64 bg-white dark:bg-gray-800 p-3 border-b border-gray-200 dark:border-gray-700 shadow-md z-10">
                <div class="max-w-4xl mx-auto flex justify-between items-center">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Sisa Waktu</h4>
                    <div class="text-xl font-bold px-4 py-2 rounded-lg" :class="{ 'text-red-500': minutes < 5, 'text-gray-800 dark:text-gray-200': minutes >= 5 }">
                        <span x-text="minutes.toString().padStart(2, '0')"></span>:<span x-text="seconds.toString().padStart(2, '0')"></span>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-16">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $exam->name }}</h3>
                    <p class="text-gray-600 mb-6">
                        {{ $exam->description }}
                        @if($exam->duration_minutes)
                            <br>Durasi: {{ $exam->duration_minutes }} menit
                        @endif
                    </p>

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('mentee.exams.store', $exam) }}" x-ref="examForm" enctype="multipart/form-data">
                        @csrf

                        @forelse ($exam->questions as $question)
                            <div class="border-t border-gray-200 pt-6 mt-6">
                                <h4 class="text-xl font-semibold mb-3">{{ $loop->iteration }}. {{ $question->question_text }} ({{ $question->score_value }} Poin)</h4>

                                @if ($question->question_type === 'multiple_choice')
                                    <div class="space-y-2">
                                        @foreach($question->options as $option)
                                            <div class="flex items-center">
                                                <input id="q{{ $question->id }}-opt{{ $option->id }}" name="answers[{{ $question->id }}][chosen_option_id]" type="radio" value="{{ $option->id }}" class="focus:ring-brand-teal h-4 w-4 text-brand-teal border-gray-300" required>
                                                <label for="q{{ $question->id }}-opt{{ $option->id }}" class="ml-3 block text-sm text-gray-700">{{ $option->option_text }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif ($question->question_type === 'essay')
                                    <x-textarea-input name="answers[{{ $question->id }}][answer_text]" class="mt-1 block w-full" rows="5" placeholder="Tulis jawaban Anda di sini..." required></x-textarea-input>
                                @endif
                                <input type="hidden" name="answers[{{ $question->id }}][question_id]" value="{{ $question->id }}">
                                <x-input-error class="mt-2" :messages="$errors->get('answers.' . $question->id . '.chosen_option_id')" />
                                <x-input-error class="mt-2" :messages="$errors->get('answers.' . $question->id . '.answer_text')" />
                            </div>
                        @empty
                            <p class="text-gray-600 text-center">Tidak ada pertanyaan untuk ujian ini. Silakan hubungi administrator.</p>
                        @endforelse

                        <!-- Submission -->
                        <div class="flex items-center justify-end mt-8 border-t border-gray-200 pt-6">
                            <x-primary-button>
                                {{ __('Kirim Ujian') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        function timer(duration) {
            return {
                totalSeconds: duration * 60,
                minutes: Math.floor((duration * 60) / 60),
                seconds: 0,
                startTimer() {
                    if (this.totalSeconds <= 0) {
                        return;
                    }
                    const interval = setInterval(() => {
                        this.totalSeconds--;

                        this.minutes = Math.floor(this.totalSeconds / 60);
                        this.seconds = this.totalSeconds % 60;

                        if (this.totalSeconds <= 0) {
                            clearInterval(interval);
                            this.$refs.examForm.submit();
                        }
                    }, 1000);
                }
            }
        }
    </script>
</x-app-layout>
