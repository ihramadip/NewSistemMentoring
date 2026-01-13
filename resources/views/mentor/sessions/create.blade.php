<x-app-layout>
    <x-slot name="header">
        <x-page-header :title="__('Buat Sesi Baru untuk Kelompok: ' . $group->name)" subtitle="Isi detail sesi mentoring di bawah ini.">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </x-slot>
            <x-slot name="actions">
                <x-secondary-button href="{{ route('mentor.groups.show', $group) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    {{ __('Batal') }}
                </x-secondary-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <form method="POST" action="{{ route('mentor.sessions.store-for-group', $group) }}" class="space-y-6">
                        @csrf

                        <!-- Session Title -->
                        <div>
                            <x-input-label for="title" :value="__('Judul atau Topik Sesi')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Session Date -->
                        <div>
                            <x-input-label for="date" :value="__('Tanggal Sesi')" />
                            <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="old('date')" required />
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Deskripsi atau Materi (Opsional)')" />
                            <x-textarea-input id="description" name="description" class="mt-1 block w-full" rows="4">{{ old('description') }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button>
                                {{ __('Simpan Sesi') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
