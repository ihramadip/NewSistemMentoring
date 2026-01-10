<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Buat Kelompok Mentoring Baru') }}" subtitle="Tambahkan informasi detail untuk kelompok mentoring yang baru.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962A3 3 0 0115 9.185a3 3 0 01-4.5 2.72m-7.5-2.962a3 3 0 00-4.682 2.72 8.982 8.982 0 003.741.479M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.mentoring-groups.store') }}">
                        @csrf

                        <!-- Group Name -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Nama Kelompok')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- Mentor -->
                        <div class="mb-4">
                            <x-input-label for="mentor_id" :value="__('Mentor')" />
                            <x-select-input id="mentor_id" name="mentor_id" class="mt-1 block w-full">
                                <option value="">{{ __('Pilih Mentor') }}</option>
                                @foreach($mentors as $mentor)
                                    <option value="{{ $mentor->id }}" {{ old('mentor_id') == $mentor->id ? 'selected' : '' }}>
                                        {{ $mentor->name }} ({{ $mentor->email }})
                                    </option>
                                @endforeach
                            </x-select-input>
                            <x-input-error class="mt-2" :messages="$errors->get('mentor_id')" />
                        </div>

                        <!-- Level -->
                        <div class="mb-4">
                            <x-input-label for="level_id" :value="__('Level Mentoring')" />
                            <x-select-input id="level_id" name="level_id" class="mt-1 block w-full">
                                <option value="">{{ __('Pilih Level') }}</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </x-select-input>
                            <x-input-error class="mt-2" :messages="$errors->get('level_id')" />
                        </div>

                        <!-- Schedule Info -->
                        <div class="mb-4">
                            <x-input-label for="schedule_info" :value="__('Jadwal (Contoh: Setiap Selasa, 14:00 - 15:00 via Zoom)')" />
                            <x-text-input id="schedule_info" name="schedule_info" type="text" class="mt-1 block w-full" :value="old('schedule_info')" />
                            <x-input-error class="mt-2" :messages="$errors->get('schedule_info')" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.mentoring-groups.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Kelompok') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
