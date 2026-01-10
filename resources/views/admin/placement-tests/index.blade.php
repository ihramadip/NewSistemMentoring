<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Hasil Placement Test') }}" subtitle="Nilai dan tentukan level untuk setiap mentee berdasarkan hasil tes.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a5.25 5.25 0 015.25 5.25c0 3.866-3.134 7-7 7a7.001 7.001 0 01-5.032-1.928l-1.47 1.47A1.5 1.5 0 013 18.75V4.5A1.5 1.5 0 014.5 3h11.25A1.5 1.5 0 0117.25 4.5v1.875c-.21-.06-.427-.105-.648-.138" />
                </svg>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">All Test Results</h3>
                    
                    @if(session('success'))
                        <div class="bg-teal-100 border border-teal-400 text-teal-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mentee Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NPM</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Audio Score</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Theory Score</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Final Level</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($testResults as $result)
                                    <tr class="@if($loop->even) bg-brand-mist @endif border-b border-gray-200 hover:bg-brand-sky/40">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $result->mentee->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $result->mentee->npm ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $result->audio_reading_score ?? 'Not graded' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $result->theory_score ?? 'Not graded' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $result->finalLevel ? 'text-gray-900' : 'text-gray-400' }}">
                                            {{ $result->finalLevel->name ?? 'Not assigned' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.placement-tests.edit', $result) }}" class="text-brand-teal hover:text-brand-gold">Grade / Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No placement test results found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $testResults->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
