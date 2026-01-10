<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Edit Pengumuman') }}" subtitle="Mengubah pengumuman '{{ Str::limit($announcement->title, 30) }}'.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12.75h-3.375M12 17.25a3.375 3.375 0 01-3.375-3.375v-1.5a1.125 1.125 0 011.125-1.125h3.375m-3.375 0s-1.125.225-1.125 1.125v1.5a3.375 3.375 0 01-3.375 3.375m0-12.75h3.375" />
                </svg>
            </x-slot>
            <x-slot name="actions">
                <a href="{{ route('admin.announcements.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
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
                    <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}">
                        @csrf
                        @method('PATCH')
                        <div>
                            <x-input-label for="title" :value="__('Judul')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $announcement->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="content" :value="__('Isi Pengumuman')" />
                            <textarea id="content" name="content" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="10">{{ old('content', $announcement->content) }}</textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>
                         <div class="mt-4">
                            <x-input-label for="target_role" :value="__('Target (Opsional: mentor, mentee)')" />
                            <x-text-input id="target_role" class="block mt-1 w-full" type="text" name="target_role" :value="old('target_role', $announcement->target_role)" />
                            <x-input-error :messages="$errors->get('target_role')" class="mt-2" />
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.announcements.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-app-layout>
    