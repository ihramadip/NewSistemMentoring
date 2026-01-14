<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Dashboard Admin') }}" subtitle="Selamat datang, {{ Auth::user()->name }}! Berikut adalah ringkasan aktivitas sistem.">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25a2.25 2.25 0 01-2.25-2.25v-2.25z" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <!-- Card 1 -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl transform hover:-translate-y-1 transition-transform duration-300 ease-in-out">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-brand-teal" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Mentor</dt>
                                    <dd class="text-3xl font-bold text-gray-900">{{ $mentorCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl transform hover:-translate-y-1 transition-transform duration-300 ease-in-out">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-brand-purple" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Mentee</dt>
                                    <dd class="text-3xl font-bold text-gray-900">{{ $menteeCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl transform hover:-translate-y-1 transition-transform duration-300 ease-in-out">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-brand-blue" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Kelompok</dt>
                                    <dd class="text-3xl font-bold text-gray-900">{{ $groupCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl transform hover:-translate-y-1 transition-transform duration-300 ease-in-out">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-brand-red" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pendaftar Pending</dt>
                                    <dd class="text-3xl font-bold {{ $pendingApplicationsCount > 0 ? 'text-red-500' : 'text-gray-900' }}">{{ $pendingApplicationsCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Chart 1: Mentees by Faculty -->
                    <div class="bg-white p-6 shadow-lg rounded-xl">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Grafik Mentee per Fakultas</h3>
                        <div class="h-80">
                            <canvas id="menteeByFacultyChart"></canvas>
                        </div>
                    </div>
                    <!-- Chart 2: Mentor Applications -->
                    <div class="bg-white p-6 shadow-lg rounded-xl">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Pendaftaran Mentor (7 Hari Terakhir)</h3>
                         <div class="h-80">
                            <canvas id="applicationsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-8">
                    <!-- New Mentor Applications -->
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800">Pendaftaran Mentor Baru</h3>
                            <div class="mt-4 flow-root">
                                <ul role="list" class="-my-5 divide-y divide-gray-200">
                                    @forelse($newApplications as $app)
                                        <li class="py-4">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-shrink-0">
                                                     <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($app->user->name) }}&color=7F9CF5&background=EBF4FF" alt="">
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $app->user->name }}</p>
                                                    <p class="text-sm text-gray-500 truncate">{{ $app->created_at->diffForHumans() }}</p>
                                                </div>
                                                <div>
                                                    <a href="{{ route('admin.mentor-applications.edit', $app) }}" class="inline-flex items-center shadow-sm px-2.5 py-0.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50">
                                                        Lihat
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="py-4 text-center text-sm text-gray-500">Tidak ada pendaftaran baru.</li>
                                    @endforelse
                                </ul>
                            </div>
                            <div class="mt-6">
                                <a href="{{ route('admin.mentor-applications.index') }}" class="w-full flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-brand-teal hover:bg-brand-teal/90">
                                    Lihat Semua Pendaftaran
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Recent Announcements -->
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl">
                        <div class="p-6">
                             <h3 class="text-lg font-semibold text-gray-800">Pengumuman Terbaru</h3>
                             <div class="mt-4 flow-root">
                                <ul role="list" class="-my-5 divide-y divide-gray-200">
                                     @forelse($latestAnnouncements as $announcement)
                                         <li class="py-4">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $announcement->title }}</p>
                                                    <p class="text-sm text-gray-500 truncate">Diterbitkan {{ $announcement->published_at->diffForHumans() }}</p>
                                                </div>
                                                <div>
                                                    <a href="{{ route('admin.announcements.show', $announcement) }}" class="inline-flex items-center shadow-sm px-2.5 py-0.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50">
                                                        Lihat
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                     @empty
                                        <li class="py-4 text-center text-sm text-gray-500">Tidak ada pengumuman terbaru.</li>
                                     @endforelse
                                </ul>
                             </div>
                             <div class="mt-6">
                                <a href="{{ route('admin.announcements.index') }}" class="w-full flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-brand-purple hover:bg-brand-purple/90">
                                    Lihat Semua Pengumuman
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Chart.js: Mentee by Faculty Chart
            const facultyCtx = document.getElementById('menteeByFacultyChart').getContext('2d');
            const facultyChart = new Chart(facultyCtx, {
                type: 'bar',
                data: {
                    labels: @json($facultyLabels),
                    datasets: [{
                        label: 'Jumlah Mentee',
                        data: @json($facultyData),
                        backgroundColor: 'rgba(23, 191, 159, 0.7)', // brand-teal with opacity
                        borderColor: 'rgba(23, 191, 159, 1)', // brand-teal
                        borderWidth: 2,
                        borderRadius: 5,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // Ensure y-axis labels are integers
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#242a3e', // brand-ink
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.raw} Mentee`;
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    }
                }
            });

            // Chart.js: Mentor Applications Chart
            const applicationsCtx = document.getElementById('applicationsChart').getContext('2d');
            const applicationsChart = new Chart(applicationsCtx, {
                type: 'line',
                data: {
                    labels: @json($applicationLabels),
                    datasets: [{
                        label: 'Pendaftar',
                        data: @json($applicationData),
                        fill: true,
                        backgroundColor: 'rgba(105, 65, 198, 0.2)', // brand-purple with opacity
                        borderColor: 'rgba(105, 65, 198, 1)', // brand-purple
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0 // Ensure y-axis labels are integers
                            }
                        }
                    },
                     plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#242a3e', // brand-ink
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.raw} Pendaftar`;
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    }
                }
            });
        });
    </script>
    @endpush

</x-app-layout>
