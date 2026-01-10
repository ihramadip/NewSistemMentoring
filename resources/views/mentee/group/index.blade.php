<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Kelompok Mentoring Saya') }}" subtitle="Informasi detail mengenai kelompok mentoring Anda dan anggotanya.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962A3 3 0 0115 9.185a3 3 0 01-4.5 2.72m-7.5-2.962a3 3 0 00-4.682 2.72 8.982 8.982 0 003.741.479M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Informasi Kelompok</h3>

                    @if(session('warning'))
                        <div class="bg-orange-100 border border-orange-400 text-orange-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('warning') }}</span>
                        </div>
                    @endif

                    @if ($mentoringGroup)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Group Details -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
                                <div>
                                    <div class="flex items-center mb-3">
                                        <svg class="h-6 w-6 text-brand-teal mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-4.67c.12-.313.253-.617.4-1.022a4.125 4.125 0 00-7.533-2.493c-3.693 0-6.105 2.818-6.105 6.375a6.375 6.375 0 004.121 5.952v-.003c1.113 0 2.16-.285 3.07-.786z" />
                                        </svg>
                                        <h4 class="text-lg font-bold text-gray-800">Nama Kelompok: {{ $mentoringGroup->name }}</h4>
                                    </div>
                                    <p class="text-sm text-gray-700 mt-2">Level: {{ $mentoringGroup->level->name ?? 'Belum Ditetapkan' }}</p>
                                    <p class="text-sm text-gray-700">Jadwal: {{ $mentoringGroup->schedule_info ?? 'Belum ditentukan' }}</p>
                                </div>
                            </div>

                            <!-- Mentor Details -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
                                <div>
                                    <div class="flex items-center mb-3">
                                        <svg class="h-6 w-6 text-brand-teal mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0H4.501z" />
                                        </svg>
                                        <h4 class="text-lg font-bold text-gray-800">Mentor Anda: {{ $mentoringGroup->mentor->name ?? 'Belum Ditetapkan' }}</h4>
                                    </div>
                                    <p class="text-sm text-gray-700 mt-2">Email Mentor: {{ $mentoringGroup->mentor->email ?? 'N/A' }}</p>
                                    <!-- Add more mentor details if needed -->
                                </div>
                            </div>

                            <!-- Group Members -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-200 md:col-span-2 lg:col-span-1">
                                <div>
                                    <div class="flex items-center mb-3">
                                        <svg class="h-6 w-6 text-brand-teal mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962A3 3 0 0115 9.185a3 3 0 01-4.5 2.72m-7.5-2.962a3 3 0 00-4.682 2.72 8.982 8.982 0 003.741.479M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h4 class="text-lg font-bold text-gray-800">Anggota Kelompok Lain:</h4>
                                    </div>
                                    <ul class="mt-2 space-y-1">
                                        @forelse ($mentoringGroup->members as $member)
                                            @if ($member->id !== Auth::id()) {{-- Don't list self --}}
                                                <li class="flex items-center text-sm text-gray-700">
                                                    <svg class="h-4 w-4 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0H4.501z" />
                                                    </svg>
                                                    {{ $member->name }} ({{ $member->npm ?? 'N/A' }})
                                                </li>
                                            @endif
                                        @empty
                                            <p class="text-gray-600">Tidak ada anggota lain di kelompok ini.</p>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 text-center col-span-full">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962A3 3 0 0115 9.185a3 3 0 01-4.5 2.72m-7.5-2.962a3 3 0 00-4.682 2.72 8.982 8.982 0 003.741.479M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-4 text-sm text-gray-500">Anda belum ditugaskan ke kelompok mentoring mana pun saat ini.</p>
                            <p class="mt-2 text-sm text-gray-500">Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
