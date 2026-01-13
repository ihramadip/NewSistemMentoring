<x-app-layout>
    <x-slot name="header">
        <x-page-header>
            <x-slot name="icon">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </x-slot>
            <x-slot name="title">
                Laporan & Statistik
            </x-slot>
            <x-slot name="subtitle">
                Analisis data mentee, nilai placement test, dan distribusi level per fakultas serta program studi.
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12" x-data="{ tab: 'ringkasan' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Tab Navigation -->
            <div class="mb-8 border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="tab = 'ringkasan'" 
                            :class="{ 'border-brand-purple text-brand-purple': tab === 'ringkasan', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'ringkasan' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Ringkasan & Demografi
                    </button>
                    <button @click="tab = 'perbandingan'" 
                            :class="{ 'border-brand-purple text-brand-purple': tab === 'perbandingan', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'perbandingan' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Analisis Perbandingan
                    </button>
                    <button @click="tab = 'individu'" 
                            :class="{ 'border-brand-purple text-brand-purple': tab === 'individu', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'individu' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Analisis Individu
                    </button>
                </nav>
            </div>

            <!-- Ringkasan & Demografi Tab -->
            <div x-show="tab === 'ringkasan'" class="space-y-8">
                <!-- Stats per Faculty -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-semibold mb-4">Statistik Placement Test per Fakultas</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fakultas</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jumlah Mentee</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Avg. Skor Bacaan</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Avg. Skor Teori</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($facultyStats as $stat)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $stat->faculty_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $stat->mentee_count }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ number_format($stat->avg_audio_score, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ number_format($stat->avg_theory_score, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Data tidak ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Level Distribution per Faculty -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-semibold mb-4">Distribusi Level per Fakultas</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fakultas</th>
                                        @foreach($levels as $level)
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ $level->name }}</th>
                                        @endforeach
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($levelDistribution as $faculty => $data)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $faculty }}</td>
                                            @foreach($levels as $level)
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $data[$level->name] ?? 0 }}</td>
                                            @endforeach
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $data['total'] }}</td>
                                        </tr>
                                    @empty
                                         <tr>
                                            <td colspan="{{ count($levels) + 2 }}" class="px-6 py-4 text-center text-sm text-gray-500">Data tidak ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Attendance Stats -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-semibold mb-4">Statistik Kehadiran Mentee</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fakultas</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Mentee</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Hadir Placement Test</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">% Kehadiran</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Hadir Ujian Akhir</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">% Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($attendanceStats as $faculty => $stats)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $faculty }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $stats['total_mentees'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $stats['placement_attended'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 font-semibold">{{ number_format($stats['placement_percentage'], 1) }}%</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $stats['final_exam_attended'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 font-semibold">{{ number_format($stats['final_exam_percentage'], 1) }}%</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Data tidak ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analisis Perbandingan Tab -->
            <div x-show="tab === 'perbandingan'" class="space-y-8">
                <!-- Score Comparison Chart By Program-->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-semibold mb-4">Grafik Perbandingan Nilai Rata-Rata (Placement Test vs Ujian Akhir)</h3>
                        <div class="h-96">
                            <canvas id="scoreComparisonChartByProgram"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Score Progression Chart (Individual) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-semibold mb-4">Analisis Peningkatan/Penurunan Nilai (Placement Test vs Ujian Akhir) per Fakultas</h3>
                        <div class="h-96">
                            <canvas id="scoreProgressionChart"></canvas>
                        </div>
                    </div>
                </div>


                <!-- Level Progression Charts -->
                <div>
                    <h3 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Analisis Progresi Level per Fakultas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @forelse($levelProgressionByFacultyAndLevel as $facultyName => $progression)
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6 text-gray-900 dark:text-gray-100">
                                    <h4 class="text-xl font-semibold mb-4">{{ $facultyName }}</h4>
                                    <div class="h-80">
                                        <canvas id="progressionChart-{{ $loop->index }}"></canvas>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-1 md:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                                Data untuk analisis progresi level tidak ditemukan.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Analisis Individu Tab -->
            <div x-show="tab === 'individu'">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-semibold mb-4">Analisis Individu (Placement Test vs Ujian Akhir)</h3>
                        
                        <form method="GET" action="{{ route('admin.statistics.index') }}" class="mb-4">
                            <div class="flex items-center">
                                <input type="text" name="search" placeholder="Cari berdasarkan nama atau NPM..."
                                       class="block w-full md:w-1/3 border-gray-300 focus:border-brand-teal focus:ring-brand-teal rounded-md shadow-sm text-sm"
                                       value="{{ request('search') }}">
                                <x-primary-button type="submit" class="ml-2">
                                    Cari
                                </x-primary-button>
                            </div>
                        </form>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">NPM</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nilai Placement Test</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nilai Ujian Akhir</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($individualAnalyses as $analysis)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $analysis->npm }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $analysis->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ number_format($analysis->placement_score, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ number_format($analysis->final_exam_score, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($analysis->final_exam_score > $analysis->placement_score)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Naik</span>
                                                @elseif($analysis->final_exam_score < $analysis->placement_score)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Turun</span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Tetap</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data yang cocok dengan pencarian Anda.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $individualAnalyses->links() }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Chart 1: Score Comparison (Avg. per Program Study)
            const scoreComparisonCtx = document.getElementById('scoreComparisonChartByProgram');
            if (scoreComparisonCtx) {
                const scoreComparisonData = @json($scoreComparisonData);
                const scoreComparisonLabels = Object.keys(scoreComparisonData);
                if (scoreComparisonLabels.length > 0) {
                    const placementScores = scoreComparisonLabels.map(label => scoreComparisonData[label].placement);
                    const finalExamScores = scoreComparisonLabels.map(label => scoreComparisonData[label].final_exam);

                    new Chart(scoreComparisonCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: scoreComparisonLabels,
                            datasets: [{
                                label: 'Nilai Placement Test',
                                data: placementScores,
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }, {
                                label: 'Nilai Ujian Akhir',
                                data: finalExamScores,
                                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { y: { beginAtZero: true, max: 100 } }
                        }
                    });
                }
            }

            // Chart 2: Individual Score Progression (Up/Down/Same per Faculty)
            const scoreProgressionCtx = document.getElementById('scoreProgressionChart');
            if (scoreProgressionCtx) {
                const scoreProgressionData = @json($scoreProgressionAnalysis);
                const scoreProgressionLabels = Object.keys(scoreProgressionData);

                if (scoreProgressionLabels.length > 0) {
                    const scoreUpData = scoreProgressionLabels.map(label => scoreProgressionData[label].up);
                    const scoreDownData = scoreProgressionLabels.map(label => scoreProgressionData[label].down);
                    const scoreSameData = scoreProgressionLabels.map(label => scoreProgressionData[label].same);

                    new Chart(scoreProgressionCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: scoreProgressionLabels,
                            datasets: [
                                {
                                    label: 'Nilai Naik',
                                    data: scoreUpData,
                                    backgroundColor: 'rgba(75, 192, 192, 0.6)', // Green
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Nilai Turun',
                                    data: scoreDownData,
                                    backgroundColor: 'rgba(255, 99, 132, 0.6)', // Red
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Nilai Tetap',
                                    data: scoreSameData,
                                    backgroundColor: 'rgba(255, 206, 86, 0.6)', // Yellow
                                    borderColor: 'rgba(255, 206, 86, 1)',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: { stacked: true },
                                y: { 
                                    stacked: true,
                                    beginAtZero: true,
                                    ticks: { stepSize: 1 }
                                }
                            },
                            plugins: {
                                tooltip: { mode: 'index', intersect: false }
                            }
                        }
                    });
                }
            }

            // Charts 2: Granular Level Progression
            const progressionData = @json($levelProgressionByFacultyAndLevel);
            const levelNames = @json($levels->pluck('name'));

            Object.keys(progressionData).forEach((facultyName, index) => {
                const canvasId = `progressionChart-${index}`;
                const progressionCtx = document.getElementById(canvasId);
                if (!progressionCtx) return;

                const facultyData = progressionData[facultyName];
                const levelUpData = levelNames.map(name => facultyData[name]?.up ?? 0);
                const levelDownData = levelNames.map(name => facultyData[name]?.down ?? 0);
                const levelSameData = levelNames.map(name => facultyData[name]?.same ?? 0);
                
                new Chart(progressionCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: levelNames,
                        datasets: [
                            {
                                label: 'Level Naik',
                                data: levelUpData,
                                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Level Turun',
                                data: levelDownData,
                                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Level Tetap',
                                data: levelSameData,
                                backgroundColor: 'rgba(255, 206, 86, 0.6)',
                                borderColor: 'rgba(255, 206, 86, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { stacked: false },
                            y: { 
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1 // Ensure y-axis only shows whole numbers
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        }
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
