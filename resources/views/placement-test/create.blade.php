<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Placement Test') }}" subtitle="Lengkapi tes bacaan dan teori untuk penentuan level mentoring Anda.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 100 15 7.5 7.5 0 000-15z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.25-5.25" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Petunjuk Pengerjaan Placement Test</h3>
                    <p class="text-gray-600 mb-6">
                        Silakan kerjakan kedua bagian tes di bawah ini dengan seksama untuk menentukan level mentoring Anda.
                    </p>

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('placement-test.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Part 1: Audio Reading Test -->
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-xl font-semibold mb-2">Bagian 1: Tes Bacaan (Tilawah)</h4>
                            <p class="mb-4 text-gray-600">
                                Silakan rekam bacaan Anda untuk **Surat Al-Fatihah** dengan tartil dan tajwid yang benar. Unggah rekaman dalam format MP3, WAV, atau M4A (maksimal 10MB).
                            </p>
                            <div>
                                <x-input-label for="audio_recording" :value="__('Unggah Rekaman Audio')" />
                                <input id="audio_recording" name="audio_recording" type="file" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-brand-teal focus:ring-brand-teal" accept=".mp3,.wav,.m4a,.ogg" required />
                                <x-input-error class="mt-2" :messages="$errors->get('audio_recording')" />
                            </div>
                        </div>

                        <!-- Part 2: Theory Test -->
                        <div class="border-t border-gray-200 mt-8 pt-6">
                            <h4 class="text-xl font-semibold mb-4">Bagian 2: Tes Teori Tajwid</h4>
                            <div class="space-y-6">
                                @foreach($questions as $id => $questionData)
                                    <fieldset>
                                        <legend class="text-base font-medium text-gray-900">{{ $loop->iteration }}. {{ $questionData['question'] }}</legend>
                                        <div class="mt-4 space-y-2">
                                            @foreach($questionData['options'] as $option)
                                                <div class="flex items-center">
                                                    <input id="question-{{ $id }}-{{ $loop->index }}" name="answers[{{ $id }}]" type="radio" value="{{ $option }}" class="focus:ring-brand-teal h-4 w-4 text-brand-teal border-gray-300" required>
                                                    <label for="question-{{ $id }}-{{ $loop->index }}" class="ml-3 block text-sm text-gray-700">{{ $option }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </fieldset>
                                @endforeach
                                <x-input-error class="mt-2" :messages="$errors->get('answers')" />
                            </div>
                        </div>

                        <!-- Submission -->
                        <div class="flex items-center justify-end mt-8 border-t border-gray-200 pt-6">
                            <x-primary-button>
                                {{ __('Selesai & Kirim Jawaban') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
