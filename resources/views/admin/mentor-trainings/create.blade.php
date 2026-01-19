<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Tambah Pelatihan Mentor') }}">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <form action="{{ route('admin.mentor-trainings.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Title -->
                        <div>
                            <x-input-label for="title" :value="__('Judul Pelatihan')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Type -->
                        <div>
                            <x-input-label for="type" :value="__('Tipe Pelatihan')" />
                            <select id="type" name="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="TFM" {{ old('type') == 'TFM' ? 'selected' : '' }}>TFM (Training for Mentor)</option>
                                <option value="Diklat" {{ old('type') == 'Diklat' ? 'selected' : '' }}>Diklat Pementor</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Deskripsi')" />
                            <x-textarea-input id="description" name="description" class="mt-1 block w-full">{{ old('description') }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Schedule Date -->
                        <div>
                            <x-input-label for="schedule_date" :value="__('Tanggal Pelaksanaan')" />
                            <x-text-input id="schedule_date" class="block mt-1 w-full" type="date" name="schedule_date" :value="old('schedule_date')" required />
                            <x-input-error :messages="$errors->get('schedule_date')" class="mt-2" />
                        </div>

                        <!-- Schedule Time -->
                        <div>
                            <x-input-label for="schedule_time" :value="__('Waktu Pelaksanaan (contoh: 09:00 - 12:00)')" />
                            <x-text-input id="schedule_time" class="block mt-1 w-full" type="text" name="schedule_time" :value="old('schedule_time')" />
                            <x-input-error :messages="$errors->get('schedule_time')" class="mt-2" />
                        </div>

                        <!-- Material Link -->
                        <div>
                            <x-input-label for="material_link" :value="__('Link Materi (URL)')" />
                            <x-text-input id="material_link" class="block mt-1 w-full" type="url" name="material_link" :value="old('material_link')" />
                            <x-input-error :messages="$errors->get('material_link')" class="mt-2" />
                        </div>

                        <!-- Test Link -->
                        <div>
                            <x-input-label for="test_link" :value="__('Link Tes (URL)')" />
                            <x-text-input id="test_link" class="block mt-1 w-full" type="url" name="test_link" :value="old('test_link')" />
                            <x-input-error :messages="$errors->get('test_link')" class="mt-2" />
                        </div>
                        
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.mentor-trainings.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Pelatihan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
