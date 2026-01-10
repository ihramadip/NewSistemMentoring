<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Grade Placement Test') }}
        </h2>
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
