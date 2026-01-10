<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Mentor Application') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Application for {{ $mentorApplication->user->name }}</h3>

                    <form method="POST" action="{{ route('admin.mentor-applications.update', $mentorApplication) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Status -->
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <x-select-input id="status" name="status" class="mt-1 block w-full">
                                <option value="pending" {{ $mentorApplication->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="accepted" {{ $mentorApplication->status === 'accepted' ? 'selected' : '' }}>Accepted</option>
                                <option value="rejected" {{ $mentorApplication->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </x-select-input>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <!-- Notes from Reviewer -->
                        <div class="mt-4">
                            <x-input-label for="notes_from_reviewer" :value="__('Notes from Reviewer')" />
                            <x-textarea-input id="notes_from_reviewer" name="notes_from_reviewer" class="mt-1 block w-full" rows="5">{{ old('notes_from_reviewer', $mentorApplication->notes_from_reviewer) }}</x-textarea-input>
                            <x-input-error class="mt-2" :messages="$errors->get('notes_from_reviewer')" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.mentor-applications.index') }}" class="text-sm font-semibold leading-6 text-gray-900 mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Update Application') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Define a custom select-input component if it doesn't exist -->
@once
    @php
        // This is a simple select-input component. You might want to create a dedicated Blade component for this.
        // For now, it's defined here for immediate use.
    @endphp
    @if (!Blade::hasComponent('select-input'))
        @php
            Blade::component('select-input', '<select {{ $attributes->merge(["class" => "border-gray-300 focus:border-brand-teal focus:ring-brand-teal rounded-md shadow-sm"]) }}>{{ $slot }}</select>');
            Blade::component('textarea-input', '<textarea {{ $attributes->merge(["class" => "border-gray-300 focus:border-brand-teal focus:ring-brand-teal rounded-md shadow-sm"]) }}>{{ $slot }}</textarea>');
        @endphp
    @endif
@endonce
