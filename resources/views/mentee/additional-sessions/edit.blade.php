<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Edit Sesi Tambahan') }}">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <form action="{{ route('additional-sessions.update', $additionalSession) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Topic -->
                        <div>
                            <x-input-label for="topic" :value="__('Materi / Topik Pertemuan')" />
                            <x-text-input id="topic" class="block mt-1 w-full" type="text" name="topic" :value="old('topic', $additionalSession->topic)" required autofocus />
                            <x-input-error :messages="$errors->get('topic')" class="mt-2" />
                        </div>

                        <!-- Date -->
                        <div>
                            <x-input-label for="date" :value="__('Tanggal Pelaksanaan')" />
                            <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="old('date', $additionalSession->date)" required />
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="belum" {{ old('status', $additionalSession->status) == 'belum' ? 'selected' : '' }}>Belum Dilaksanakan</option>
                                <option value="sudah" {{ old('status', $additionalSession->status) == 'sudah' ? 'selected' : '' }}>Sudah Dilaksanakan</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Proof -->
                        <div>
                            <x-input-label for="proof" :value="__('Bukti (Screenshot/Foto)')" />
                            <input id="proof" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" name="proof">
                            <p class="mt-1 text-sm text-gray-500" id="file_input_help">PNG, JPG (MAX. 2MB). Kosongkan jika tidak ingin mengubah bukti.</p>
                            @if($additionalSession->proof_path)
                                <div class="mt-4">
                                    <p class="text-sm font-medium text-gray-700">Bukti Saat Ini:</p>
                                    <img src="{{ Storage::url($additionalSession->proof_path) }}" alt="Bukti" class="mt-2 rounded-md max-w-xs">
                                </div>
                            @endif
                            <x-input-error :messages="$errors->get('proof')" class="mt-2" />
                        </div>
                        
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('mentee.sessions.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Perbarui Sesi') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
