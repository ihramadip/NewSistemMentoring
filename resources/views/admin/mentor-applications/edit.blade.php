<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Review Pendaftaran: {{ $mentorApplication->user->name }}" subtitle="Review data pendaftar dan berikan penilaian (Accepted/Rejected).">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
            </x-slot>
            <x-slot name="actions">
                <a href="{{ route('admin.mentor-applications.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                    </svg>
                    Kembali
                </a>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Application Details Column -->
            <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Data Pendaftar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nama</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $mentorApplication->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $mentorApplication->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Applied On</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $mentorApplication->created_at->format('d M Y H:i') }}</p>
                        </div>
                         <div>
                            <p class="text-sm font-medium text-gray-500">CV</p>
                             <p class="mt-1 text-sm">
                                <a href="{{ route('admin.mentor-applications.cv', $mentorApplication) }}" target="_blank" class="text-brand-teal hover:underline font-semibold">Download CV</a>
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">Riwayat BTAQ</p>
                            <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $mentorApplication->btaq_history ?? 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500 mb-2">Rekaman Bacaan</p>
                            <audio controls class="w-full">
                                <source src="{{ route('admin.mentor-applications.audio', $mentorApplication) }}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessment Form Column -->
            <div class="lg:col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                     <h3 class="text-lg font-medium text-gray-900 mb-4">Form Penilaian</h3>
                    <form method="POST" action="{{ route('admin.mentor-applications.update', $mentorApplication) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Status -->
                        <div>
                            <x-input-label for="status" :value="__('Status Penilaian')" />
                            <x-select-input id="status" name="status" class="mt-1 block w-full">
                                <option value="pending" @selected($mentorApplication->status === 'pending')>Pending</option>
                                <option value="accepted" @selected($mentorApplication->status === 'accepted')>Accepted</option>
                                <option value="rejected" @selected($mentorApplication->status === 'rejected')>Rejected</option>
                            </x-select-input>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <!-- Notes from Reviewer -->
                        <div class="mt-4">
                            <x-input-label for="notes_from_reviewer" :value="__('Catatan untuk Pendaftar (Opsional)')" />
                            <x-textarea-input id="notes_from_reviewer" name="notes_from_reviewer" class="mt-1 block w-full" rows="8">{{ old('notes_from_reviewer', $mentorApplication->notes_from_reviewer) }}</x-textarea-input>
                            <x-input-error class="mt-2" :messages="$errors->get('notes_from_reviewer')" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Simpan Penilaian') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
