<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Edit Kelompok Mentoring') }}" subtitle="Ubah detail kelompok mentoring dan kelola anggotanya.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14.25v4.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 18v-7.5A2.25 2.25 0 016.75 8.25h7.5" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.mentoring-groups.update', $mentoringGroup) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Group Name -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Nama Kelompok')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $mentoringGroup->name)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- Mentor -->
                        <div class="mb-4">
                            <x-input-label for="mentor_id" :value="__('Mentor')" />
                            <x-select-input id="mentor_id" name="mentor_id" class="mt-1 block w-full">
                                <option value="">{{ __('Pilih Mentor') }}</option>
                                @foreach($mentors as $mentor)
                                    <option value="{{ $mentor->id }}" {{ old('mentor_id', $mentoringGroup->mentor_id) == $mentor->id ? 'selected' : '' }}>
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
                                    <option value="{{ $level->id }}" {{ old('level_id', $mentoringGroup->level_id) == $level->id ? 'selected' : '' }}>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </x-select-input>
                            <x-input-error class="mt-2" :messages="$errors->get('level_id')" />
                        </div>

                        <!-- Schedule Info -->
                        <div class="mb-4">
                            <x-input-label for="schedule_info" :value="__('Jadwal (Contoh: Setiap Selasa, 14:00 - 15:00 via Zoom)')" />
                            <x-text-input id="schedule_info" name="schedule_info" type="text" class="mt-1 block w-full" :value="old('schedule_info', $mentoringGroup->schedule_info)" />
                            <x-input-error class="mt-2" :messages="$errors->get('schedule_info')" />
                        </div>

                        <!-- Mentees Assignment (Multi-select) -->
                        <div class="mb-4">
                            <x-input-label for="mentee_ids" :value="__('Anggota Mentee (Pilih dari daftar)')" />
                            <select id="mentee_ids" name="mentee_ids[]" multiple class="mt-1 block w-full border-gray-300 focus:border-brand-teal focus:ring-brand-teal rounded-md shadow-sm">
                                @foreach($mentees as $mentee)
                                    <option value="{{ $mentee->id }}" {{ in_array($mentee->id, old('mentee_ids', $mentoringGroup->members->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $mentee->name }} ({{ $mentee->npm }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('mentee_ids')" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.mentoring-groups.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Kelompok') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
