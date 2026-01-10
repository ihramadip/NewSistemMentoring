<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Nilai Placement Test') }}" subtitle="Menilai hasil tes untuk mentee '{{ $placementTest->mentee->name }}'.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a5.25 5.25 0 015.25 5.25c0 3.866-3.134 7-7 7a7.001 7.001 0 01-5.032-1.928l-1.47 1.47A1.5 1.5 0 013 18.75V4.5A1.5 1.5 0 014.5 3h11.25A1.5 1.5 0 0117.25 4.5v1.875c-.21-.06-.427-.105-.648-.138" />
                </svg>
            </x-slot>
            <x-slot name="actions">
                <a href="{{ route('admin.placement-tests.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        Grading for: {{ $placementTest->mentee->name }} ({{ $placementTest->mentee->npm }})
                    </h3>
                                            <p class="text-sm text-gray-600 mb-4">
                                            Enter the scores and assign the final mentoring level.
                                        </p>
                    
                                        @if($placementTest->audio_recording_path)
                                            <div class="mb-6">
                                                <h5 class="text-md font-semibold text-gray-800 mb-2">Listen to Audio Recording:</h5>
                                                <audio controls class="w-full">
                                                    <source src="{{ route('admin.placement-tests.audio', $placementTest) }}" type="audio/mpeg">
                                                    Your browser does not support the audio element.
                                                </audio>
                                            </div>
                                        @endif
                    
                                        <form method="POST" action="{{ route('admin.placement-tests.update', $placementTest) }}">                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Audio Reading Score -->
                            <div>
                                <x-input-label for="audio_reading_score" :value="__('Audio Reading Score (0-100)')" />
                                <x-text-input id="audio_reading_score" name="audio_reading_score" type="number" class="mt-1 block w-full" :value="old('audio_reading_score', $placementTest->audio_reading_score)" min="0" max="100" />
                                <x-input-error class="mt-2" :messages="$errors->get('audio_reading_score')" />
                            </div>

                            <!-- Theory Score -->
                            <div>
                                <x-input-label for="theory_score" :value="__('Theory Score (0-100)')" />
                                <x-text-input id="theory_score" name="theory_score" type="number" class="mt-1 block w-full" :value="old('theory_score', $placementTest->theory_score)" min="0" max="100" />
                                <x-input-error class="mt-2" :messages="$errors->get('theory_score')" />
                            </div>

                            <!-- Final Level -->
                            <div class="md:col-span-2">
                                <x-input-label for="final_level_id" :value="__('Final Assigned Level')" />
                                <x-select-input id="final_level_id" name="final_level_id" class="mt-1 block w-full">
                                    <option value="">{{ __('Assign a level') }}</option>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->id }}" {{ old('final_level_id', $placementTest->final_level_id) == $level->id ? 'selected' : '' }}>
                                            {{ $level->name }}
                                        </option>
                                    @endforeach
                                </x-select-input>
                                <x-input-error class="mt-2" :messages="$errors->get('final_level_id')" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.placement-tests.index') }}" class="text-sm font-semibold leading-6 text-gray-900 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Update Result') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
