<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mentor Application Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Application by {{ $mentorApplication->user->name }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Applicant Name:</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $mentorApplication->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Applicant Email:</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $mentorApplication->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">CV:</p>
                            <p class="mt-1 text-sm text-gray-900">
                                <a href="{{ Storage::url($mentorApplication->cv_path) }}" target="_blank" class="text-brand-teal hover:underline">View CV</a>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Recording:</p>
                            <p class="mt-1 text-sm text-gray-900">
                                <a href="{{ Storage::url($mentorApplication->recording_path) }}" target="_blank" class="text-brand-teal hover:underline">Listen Recording</a>
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">BTAQ History:</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $mentorApplication->btaq_history ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Status:</p>
                            <p class="mt-1 text-sm text-gray-900 capitalize">{{ $mentorApplication->status }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Applied On:</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $mentorApplication->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">Notes from Reviewer:</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $mentorApplication->notes_from_reviewer ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <a href="{{ route('admin.mentor-applications.index') }}" class="text-sm font-semibold leading-6 text-gray-900">Back to List</a>
                        <a href="{{ route('admin.mentor-applications.edit', $mentorApplication) }}" class="rounded-md bg-brand-teal px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-gold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-teal">Edit Application</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
