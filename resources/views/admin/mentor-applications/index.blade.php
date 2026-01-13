<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Manajemen Pendaftaran Mentor') }}" subtitle="Kelola daftar aplikasi pendaftaran mentor yang masuk.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962A3 3 0 0115 9.185a3 3 0 01-4.5 2.72m-7.5-2.962a3 3 0 00-4.682 2.72 8.982 8.982 0 003.741.479M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot>
            <x-slot name="actions">
                {{-- No specific primary actions like 'Import' for mentor applications yet --}}
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="{ selectedApplications: [], selectAll: false }">

                    @if (session('success'))
                        <div class="bg-teal-100 border border-teal-400 text-teal-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{!! session('success') !!}</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between mb-4">
                        <div x-show="selectedApplications.length > 0" class="flex items-center space-x-4">
                            <form id="bulk-delete-form" method="POST" action="{{ route('admin.mentor-applications.bulkDestroy') }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <template x-for="id in selectedApplications" :key="id">
                                    <input type="hidden" name="ids[]" :value="id">
                                </template>
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        onclick="return confirm('APAKAH ANDA YAKIN? Tindakan ini akan menghapus ' + selectedApplications.length + ' aplikasi mentor yang dipilih dan tidak dapat dikembalikan.')">
                                    Hapus <span x-text="selectedApplications.length"></span> Aplikasi Terpilih
                                </button>
                            </form>
                        </div>
                        <div x-show="selectedApplications.length === 0" class="w-full">
                            <form method="GET" action="{{ route('admin.mentor-applications.index') }}">
                                <div class="flex items-center"> {{-- Removed justify-end --}}
                                    <input type="text" name="search" placeholder="Cari berdasarkan nama atau email..."
                                           class="block w-full md:w-1/3 border-gray-300 focus:border-brand-teal focus:ring-brand-teal rounded-md shadow-sm text-sm"
                                           value="{{ request('search') }}">
                                    <x-primary-button type="submit" class="ml-2">
                                        Cari
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="border-b">
                                <tr>
                                    <th scope="col" class="p-4">
                                        <input type="checkbox"
                                               class="rounded border-gray-300 text-brand-teal focus:ring-brand-teal"
                                               @click="selectAll = !selectAll; selectedApplications = selectAll ? {{ $applications->pluck('id') }} : []">
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Pendaftar</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fakultas</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal Daftar</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($applications as $application)
                                    <tr
                                        class="@if ($loop->even) bg-brand-mist @endif border-b border-gray-200 hover:bg-brand-sky/40"
                                        :class="{'bg-yellow-100': selectedApplications.includes({{ $application->id }})}">
                                        <td class="p-4">
                                            <input type="checkbox" x-model="selectedApplications" value="{{ $application->id }}"
                                                   class="rounded border-gray-300 text-brand-teal focus:ring-brand-teal">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $application->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $application->user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $application->user->faculty->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @php
                                                $statusClass = '';
                                                if ($application->status == 'pending') {
                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                } elseif ($application->status == 'accepted') {
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                } elseif ($application->status == 'rejected') {
                                                    $statusClass = 'bg-red-100 text-red-800';
                                                }
                                            @endphp
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                {{ $application->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $application->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.mentor-applications.edit', $application) }}"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3">Review & Nilai</a>
                                            <form
                                                action="{{ route('admin.mentor-applications.destroy', $application) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Yakin ingin menghapus pendaftaran ini?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" {{-- Adjusted colspan for the new checkbox column --}}
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Belum ada data pendaftaran mentor.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $applications->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
