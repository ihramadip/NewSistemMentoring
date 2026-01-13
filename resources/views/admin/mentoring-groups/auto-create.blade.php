<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Pengelompokan Otomatis">
            <x-slot name="icon">
                <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.898 20.562L16.5 21.75l-.398-1.188a3.375 3.375 0 00-2.9-2.9L12 17.25l1.188-.398a3.375 3.375 0 002.9-2.9l.398-1.188 1.188.398a3.375 3.375 0 002.9 2.9l.398 1.188-.398 1.188a3.375 3.375 0 00-2.9 2.9l-1.188.398z" />
                </svg>
            </x-slot>
            <x-slot name="subtitle">
                Buat kelompok mentoring secara otomatis berdasarkan fakultas, level, dan gender.
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12" x-data="{ submitting: false }">
        
        <!-- Full-screen loading overlay -->
        <div x-show="submitting" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75" x-cloak>
            <div class="flex flex-col items-center text-white">
                <svg class="animate-spin -ml-1 mr-3 h-10 w-10 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="mt-4 text-xl font-semibold">Memproses Kelompok...</span>
                <span class="text-sm">Ini mungkin akan memakan waktu beberapa saat. Mohon jangan tutup halaman ini.</span>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-xl font-semibold mb-4">Konfirmasi Pengelompokan Otomatis</h3>
                    
                    <p class="mb-6 text-gray-600 dark:text-gray-400">
                        Fitur ini akan mengambil semua mentee yang belum memiliki kelompok dan semua mentor yang tersedia, kemudian membuat kelompok baru berdasarkan kriteria berikut:
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Mentee dikelompokkan berdasarkan **Fakultas**, **Level Placement Test**, dan **Gender**.</li>
                            <li>Setiap kelompok akan berisi sekitar **14 mentee**.</li>
                            <li>Setiap kelompok akan ditugaskan ke satu mentor yang tersedia.</li>
                        </ul>
                    </p>

                    <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Mentee Belum Berkelompok</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">{{ $unassignedMenteesCount }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Mentor Tersedia</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">{{ $availableMentorsCount }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Estimasi Kelompok Baru</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">~{{ $estimatedGroups }}</p>
                        </div>
                    </div>

                    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                        <p class="font-bold">Peringatan</p>
                        <p>Tindakan ini akan membuat banyak data baru dan tidak dapat diurungkan dengan mudah. Pastikan data mentee dan mentor sudah final sebelum melanjutkan.</p>
                    </div>

                    <form method="POST" action="{{ route('admin.mentoring-groups.auto-grouping.store') }}" @submit="submitting = true">
                        @csrf
                        <x-primary-button type="submit" x-bind:disabled="submitting">
                            <span x-show="!submitting" class="flex items-center">
                                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0011.667 0l3.181-3.183m-4.991-2.691V5.25a3.375 3.375 0 00-3.375-3.375H8.25a3.375 3.375 0 00-3.375 3.375v2.25m-1.125 4.5l3.183 3.181a8.25 8.25 0 0011.667 0l3.183-3.181" />
                                </svg>
                                Generate Kelompok Sekarang
                            </span>
                            <span x-show="submitting" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </x-primary-button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
