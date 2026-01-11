<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Ujian Selesai') }}" subtitle="Hasil ujian Anda telah berhasil direkam.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900 text-center">
                    
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                      </div>

                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Terima Kasih!</h3>
                    
                    @if(session('success'))
                        <p class="text-gray-600 mb-6">{{ session('success') }}</p>
                    @else
                         <p class="text-gray-600 mb-6">Ujian Anda telah berhasil dikirimkan. Silakan tunggu hasil penilaian.</p>
                    @endif


                    <div class="mt-6">
                        <a href="{{ route('mentee.exams.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-brand-teal hover:bg-brand-gold focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2">
                            Kembali ke Daftar Ujian
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>