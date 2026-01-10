<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Dashboard Admin') }}" subtitle="Selamat datang, {{ Auth::user()->name }}! Berikut adalah ringkasan sistem.">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500">Total Mentor</h4>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $mentorCount }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500">Total Mentee</h4>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $menteeCount }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500">Total Kelompok</h4>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $groupCount }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500">Pendaftar Pending</h4>
                    <p class="mt-1 text-3xl font-semibold {{ $pendingApplicationsCount > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $pendingApplicationsCount }}</p>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- New Mentor Applications -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800">Pendaftaran Mentor Baru</h3>
                            <div class="mt-4 space-y-4">
                                @forelse($newApplications as $app)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $app->user->name }}</p>
                                            <p class="text-sm text-gray-500">Mendaftar {{ $app->created_at->diffForHumans() }}</p>
                                        </div>
                                        <a href="{{ route('admin.mentor-applications.edit', $app) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Nilai</a>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">Tidak ada pendaftaran baru.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Quick Access -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Akses Cepat</h3>
                            <div class="space-y-3">
                                <x-primary-button href="{{ route('admin.mentor-applications.index') }}" class="w-full justify-center">Manajemen Pendaftaran Mentor</x-primary-button>
                                <x-primary-button href="{{ route('admin.mentees.index') }}" class="w-full justify-center bg-brand-teal hover:bg-brand-teal/90">Manajemen Mentee</x-primary-button>
                                <x-primary-button href="{{ route('admin.mentoring-groups.index') }}" class="w-full justify-center">Manajemen Kelompok</x-primary-button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
