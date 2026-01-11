<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Materi Belajar') }}" subtitle="Akses semua materi sesuai level Anda saat ini.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('warning'))
                        <div class="bg-orange-100 border border-orange-400 text-orange-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('warning') }}</span>
                        </div>
                    @endif

                    @if($isAdmin)
                        <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Semua Materi (Tampilan Admin)</h3>
                        @forelse($materials as $levelName => $levelMaterials)
                            <div class="mt-8">
                                <h4 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Level: <span class="text-brand-teal">{{ $levelName ?: 'Tanpa Level' }}</span></h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach($levelMaterials as $material)
                                        @include('mentee.materials.partials.material-card', ['material' => $material])
                                    @endforeach
                                </div>
                            </div>
                        @empty
                             <div class="col-span-full bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                                <p class="mt-4 text-sm text-gray-500">Belum ada materi yang dibuat sama sekali.</p>
                            </div>
                        @endforelse
                    @else
                        <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Materi untuk Level Anda: <span class="text-brand-teal">{{ $placementTest->finalLevel->name ?? 'Belum Ditetapkan' }}</span></h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse ($materials as $material)
                                @include('mentee.materials.partials.material-card', ['material' => $material])
                            @empty
                                <div class="col-span-full bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                    </svg>
                                    <p class="mt-4 text-sm text-gray-500">Belum ada materi yang tersedia untuk level Anda saat ini.</p>
                                    <p class="mt-2 text-sm text-gray-500">Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.</p>
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
