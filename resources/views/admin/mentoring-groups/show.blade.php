<x-app-layout>
    <x-slot name="header">
        <x-page-header
            :title="__('Detail Kelompok: ' . $mentoringGroup->name)"
            :subtitle="__('Mentor: ' . $mentoringGroup->mentor->name)"
        >
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m-7.5-2.962A3 3 0 0 1 15 9.185a3 3 0 0 1-4.5 2.72m-7.5-2.962a3 3 0 0 0-4.682 2.72 8.982 8.982 0 0 0 3.741.479M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </x-slot>

            <x-slot name="actions">
                <x-secondary-button href="{{ route('admin.mentoring-groups.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    {{ __('Kembali') }}
                </x-secondary-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h3 class="font-semibold text-lg text-gray-800 leading-tight mb-4">
                        Daftar Anggota (Mentee)
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        NPM
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Mentee
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Program Studi
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fakultas
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($mentoringGroup->members as $mentee)
                                    <tr class="hover:bg-brand-mist/40">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $mentee->npm }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $mentee->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $mentee->program_study }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $mentee->faculty->name ?? 'N/A' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-sm text-gray-500 text-center">
                                            Belum ada anggota di kelompok ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
