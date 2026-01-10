<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Impor Data Mentee') }}" subtitle="Unggah file CSV untuk menambahkan atau memperbarui data mentee secara massal.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15M9 12l3 3m0 0l3-3m-3 3V2.25" />
                </svg>
            </x-slot>
            <x-slot name="actions">
                <a href="{{ route('admin.mentees.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
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
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Unggah File CSV Mentee</h3>

                    @if(session('success'))
                        <div class="bg-teal-100 border border-teal-400 text-teal-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="bg-orange-100 border border-orange-400 text-orange-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{!! session('warning') !!}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.mentees.import.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div>
                            <x-input-label for="mentee_file" :value="__('Pilih File CSV')" />
                            <input id="mentee_file" name="mentee_file" type="file" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required />
                            <x-input-error class="mt-2" :messages="$errors->get('mentee_file')" />
                            <p class="mt-2 text-sm text-gray-500">Pastikan file CSV memiliki kolom: npm, nama, email, fakultas, jenis_kelamin.</p>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Impor') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
