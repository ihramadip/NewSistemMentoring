<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Buat Sesi Baru') }}" subtitle="Langkah 1: Pilih Kelompok Bimbingan">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Pilih kelompok yang akan dibuatkan sesi:</h3>
                    
                    @if($groups->isEmpty())
                        <div class="text-center py-10">
                            <p class="text-gray-500">Anda saat ini tidak memiliki kelompok bimbingan.</p>
                            <p class="mt-2">
                                <a href="{{ route('mentor.groups.index') }}" class="text-indigo-600 hover:text-indigo-900">
                                    Lihat Kelompok Bimbingan
                                </a>
                            </p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($groups as $group)
                                <div class="p-4 border rounded-lg flex items-center justify-between hover:bg-gray-50">
                                    <div>
                                        <p class="font-semibold text-brand-purple">{{ $group->name }}</p>
                                        <p class="text-sm text-gray-600">Jadwal: {{ $group->day_of_week }}, {{ $group->time_of_day }}</p>
                                    </div>
                                    <div>
                                        <a href="{{ route('mentor.sessions.create-for-group', $group) }}" class="inline-flex items-center px-4 py-2 bg-brand-teal border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:brightness-90 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Buat Sesi
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
