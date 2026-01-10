<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Detail Pendaftaran: {{ $mentorApplication->user->name }}" subtitle="Melihat detail data yang diajukan oleh calon mentor.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962A3 3 0 0115 9.185a3 3 0 01-4.5 2.72m-7.5-2.962a3 3 0 00-4.682 2.72 8.982 8.982 0 003.741.479M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot>
            <x-slot name="actions">
                <a href="{{ route('admin.mentor-applications.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                    </svg>
                    Kembali
                </a>
                 <x-primary-button href="{{ route('admin.mentor-applications.edit', $mentorApplication) }}">
                    {{ __('Nilai Pendaftaran') }}
                </x-primary-button>
            </x-slot>
        </x-page-header>
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
