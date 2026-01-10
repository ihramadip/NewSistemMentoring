<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Buat Ujian Baru') }}" subtitle="Definisikan detail ujian dan level terkait.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12H12m-2.25 4.5H12M12 18.75V15m-1.5 2.25l-1.5-1.5m1.5 1.5l1.5-1.5M12 18.75L10.5 17.25M12 18.75L13.5 17.25M12 14.25h-2.25M15 11.25H9M15 12h-2.25" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.exams.store') }}">
                        @csrf

                        <!-- Exam Name -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Nama Ujian')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Deskripsi Ujian')" />
                            <x-textarea-input id="description" name="description" class="mt-1 block w-full">{{ old('description') }}</x-textarea-input>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Level -->
                        <div class="mb-4">
                            <x-input-label for="level_id" :value="__('Level Terkait (Opsional)')" />
                            <x-select-input id="level_id" name="level_id" class="mt-1 block w-full">
                                <option value="">{{ __('Pilih Level (Semua Level)') }}</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </x-select-input>
                            <x-input-error class="mt-2" :messages="$errors->get('level_id')" />
                        </div>

                        <!-- Duration -->
                        <div class="mb-4">
                            <x-input-label for="duration_minutes" :value="__('Durasi Ujian (Menit)')" />
                            <x-text-input id="duration_minutes" name="duration_minutes" type="number" class="mt-1 block w-full" :value="old('duration_minutes')" min="1" />
                            <x-input-error class="mt-2" :messages="$errors->get('duration_minutes')" />
                        </div>

                        <!-- Published At -->
                        <div class="mb-4">
                            <x-input-label for="published_at" :value="__('Tanggal Publikasi (Opsional - Kosongkan untuk Draft)')" />
                            <x-text-input id="published_at" name="published_at" type="datetime-local" class="mt-1 block w-full" :value="old('published_at')" />
                            <x-input-error class="mt-2" :messages="$errors->get('published_at')" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.exams.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Ujian') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
