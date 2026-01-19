@php
/** @var \App\Models\User $user */
$user = Auth::user();
@endphp
<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Dashboard') }}" subtitle="Selamat datang kembali, {{ $user->name }}! Ini adalah pusat kendali Anda.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25a2.25 2.25 0 01-2.25-2.25v-2.25z"></path>
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500">Kelompok Aktif</h4>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $groupCount }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500">Total Mentee</h4>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $menteeCount }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500">Sesi Terdekat</h4>
                    <p class="mt-1 text-lg font-semibold text-gray-900">
                        {{ $upcomingSessions->first() ? $upcomingSessions->first()->date->format('d M, H:i') : 'Tidak Ada' }}
                    </p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500">Laporan Tertunda</h4>
                    <p class="mt-1 text-3xl font-semibold {{ $pendingReportsCount > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $pendingReportsCount }}</p>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Upcoming Sessions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800">Sesi Mendatang</h3>
                            <div class="mt-4 space-y-4">
                                @forelse($upcomingSessions as $session)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $session->title }}</p>
                                            <p class="text-sm text-gray-500">{{ $session->mentoringGroup->name }} &middot; {{ $session->date->diffForHumans() }}</p>
                                        </div>
                                        <a href="{{ route('mentor.sessions.show', $session) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Kelola</a>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">Tidak ada sesi yang akan datang.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- My Groups -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800">Kelompok Bimbingan</h3>
                            <ul class="mt-4 space-y-3">
                                @forelse($groups as $group)
                                    <li class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $group->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $group->level->name }}</p>
                                        </div>
                                        <a href="{{ route('mentor.groups.show', $group) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Detail</a>
                                    </li>
                                @empty
                                     <p class="text-sm text-gray-500">Anda belum memiliki kelompok.</p>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>