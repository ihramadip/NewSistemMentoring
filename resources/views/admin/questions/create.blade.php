<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Buat Pertanyaan Baru') }}" subtitle="Tambahkan pertanyaan baru untuk ujian: {{ $exam->name }}">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712L12 16.125l-2.121-2.121c-1.172-1.025-1.172-2.687 0-3.712z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 12.75l1.5 1.5M12 12.75l-1.5 1.5M12 12.75V7.5" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.exams.questions.store', $exam) }}">
                        @csrf

                        <!-- Question Text -->
                        <div class="mb-4">
                            <x-input-label for="question_text" :value="__('Teks Pertanyaan')" />
                            <x-textarea-input id="question_text" name="question_text" class="mt-1 block w-full" required>{{ old('question_text') }}</x-textarea-input>
                            <x-input-error class="mt-2" :messages="$errors->get('question_text')" />
                        </div>

                        <!-- Question Type -->
                        <div class="mb-4">
                            <x-input-label for="question_type" :value="__('Jenis Pertanyaan')" />
                            <x-select-input id="question_type" name="question_type" class="mt-1 block w-full" required>
                                <option value="">{{ __('Pilih Jenis Pertanyaan') }}</option>
                                @foreach($questionTypes as $key => $value)
                                    <option value="{{ $key }}" {{ old('question_type') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </x-select-input>
                            <x-input-error class="mt-2" :messages="$errors->get('question_type')" />
                        </div>

                        <!-- Score Value -->
                        <div class="mb-4">
                            <x-input-label for="score_value" :value="__('Nilai Poin')" />
                            <x-text-input id="score_value" name="score_value" type="number" class="mt-1 block w-full" :value="old('score_value')" min="1" required />
                            <x-input-error class="mt-2" :messages="$errors->get('score_value')" />
                        </div>

                        <!-- Options for Multiple Choice (placeholder for dynamic fields) -->
                        <div id="options_section" class="mb-4 p-4 border border-gray-200 rounded-md bg-gray-50 {{ old('question_type') === 'multiple_choice' ? '' : 'hidden' }}">
                            <h5 class="font-semibold text-gray-800 mb-3">Opsi Pilihan Ganda (Tandai yang Benar)</h5>
                            <div id="option_fields" class="space-y-3">
                                <!-- Example option field (can be duplicated with JS) -->
                                <div class="flex items-center space-x-2">
                                    <x-text-input name="options[0][text]" type="text" placeholder="Teks Opsi" class="flex-1" value="{{ old('options.0.text') }}" />
                                    <input type="checkbox" name="options[0][is_correct]" value="1" {{ old('options.0.is_correct') ? 'checked' : '' }} class="rounded border-gray-300 text-brand-teal shadow-sm focus:ring-brand-teal">
                                    <x-input-label for="options[0][is_correct]" value="{{ __('Benar') }}" class="ml-2" />
                                </div>
                                <!-- Add more with JS -->
                            </div>
                            <button type="button" id="add_option" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150">
                                Tambah Opsi
                            </button>
                            <x-input-error class="mt-2" :messages="$errors->get('options')" />
                            <x-input-error class="mt-2" :messages="$errors->get('options.*.text')" />
                        </div>
                        
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.exams.edit', $exam) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Pertanyaan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const questionTypeSelect = document.getElementById('question_type');
            const optionsSection = document.getElementById('options_section');
            const optionFields = document.getElementById('option_fields');
            const addOptionButton = document.getElementById('add_option');
            let optionIndex = {{ old('options') ? count(old('options')) : 1 }}; // Start with 1 if no old input, or count existing

            function toggleOptionsSection() {
                if (questionTypeSelect.value === 'multiple_choice') {
                    optionsSection.classList.remove('hidden');
                } else {
                    optionsSection.classList.add('hidden');
                }
            }

            function addOptionField(text = '', isCorrect = false) {
                const newOptionDiv = document.createElement('div');
                newOptionDiv.classList.add('flex', 'items-center', 'space-x-2');
                newOptionDiv.innerHTML = `
                    <x-text-input name="options[${optionIndex}][text]" type="text" placeholder="Teks Opsi" class="flex-1" value="${text}" required />
                    <input type="checkbox" name="options[${optionIndex}][is_correct]" value="1" ${isCorrect ? 'checked' : ''} class="rounded border-gray-300 text-brand-teal shadow-sm focus:ring-brand-teal">
                    <x-input-label value="{{ __('Benar') }}" class="ml-2" />
                    <button type="button" class="remove_option text-red-500 hover:text-red-700">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.927a2.25 2.25 0 01-2.244-2.077L4.74 5.79m14.26 0H5.5" />
                        </svg>
                    </button>
                `;
                optionFields.appendChild(newOptionDiv);
                optionIndex++;

                newOptionDiv.querySelector('.remove_option').addEventListener('click', function() {
                    newOptionDiv.remove();
                });
            }

            // Initial setup based on old input
            if (questionTypeSelect.value === 'multiple_choice' && {{ old('options') ? 'true' : 'false' }}) {
                const oldOptions = @json(old('options'));
                optionFields.innerHTML = ''; // Clear the example
                Object.values(oldOptions).forEach(option => {
                    addOptionField(option.text, option.is_correct === '1');
                });
            } else if (questionTypeSelect.value === 'multiple_choice') {
                 // Add an initial empty option if multiple_choice is selected and no old options
                 addOptionField();
            }

            questionTypeSelect.addEventListener('change', toggleOptionsSection);
            addOptionButton.addEventListener('click', () => addOptionField());

            // Initial toggle check
            toggleOptionsSection();
        });
    </script>
    @endpush
</x-app-layout>
