<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Impor Mentee') }}
        </h2>
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
