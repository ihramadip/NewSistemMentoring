<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Kelompok Bimbingan Saya') }}" subtitle="Daftar kelompok mentoring yang Anda bimbing beserta anggotanya.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.028.658a.79.79 0 01-.588.792l-.028.008a.786.786 0 01-.766-.36zM14 8a2 2 0 11-4 0 2 2 0 014 0zM10 18a6.484 6.484 0 005.065-2.915 3 3 0 01-4.308-3.516.78.78 0 01.358-.442l.028.008a.79.79 0 01.588.792.786.786 0 01-.766.36 4.486 4.486 0 01-1.905 3.959c-.023.222-.014.442.028.658a.79.79 0 01-.588.792l-.028.008a.786.786 0 01-.766-.36z" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($groups->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 text-center">
                        <h3 class="text-lg font-medium">Anda belum memiliki kelompok bimbingan.</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Silakan hubungi admin untuk informasi lebih lanjut.
                        </p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($groups as $group)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col">
                            <div class="p-6 flex-grow">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $group->name }}</h3>
                                <p class="mt-1 text-sm font-medium text-brand-teal">{{ $group->level->name }}</p>

                                <div class="mt-4 pt-4 border-t">
                                    <h4 class="text-xs font-semibold uppercase text-gray-500 mb-2">
                                        Anggota ({{ $group->members->count() }})
                                    </h4>
                                    <ul class="space-y-1">
                                        @forelse($group->members as $member)
                                            <li class="text-sm text-gray-700 truncate">{{ $member->name }}</li>
                                        @empty
                                            <li class="text-sm text-gray-500">Belum ada anggota.</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                            <div class="p-4 bg-gray-50 border-t">
                                <a href="{{ route('mentor.groups.show', $group) }}" class="w-full text-center inline-flex items-center justify-center px-4 py-2 bg-brand-ink border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-ink/90 focus:outline-none focus:ring-2 focus:ring-brand-ink focus:ring-offset-2 transition ease-in-out duration-150">
                                    Lihat Detail & Sesi
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
