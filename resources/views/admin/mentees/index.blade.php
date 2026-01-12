<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Manajemen Mentee') }}" subtitle="Lihat, cari, dan kelola semua data mentee yang terdaftar di sistem.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-4.67c.12-.313.253-.617.4-1.022a4.125 4.125 0 00-7.533-2.493c-3.693 0-6.105 2.818-6.105 6.375a6.375 6.375 0 004.121 5.952v-.003c1.113 0 2.16-.285 3.07-.786z" />
                </svg>
            </x-slot>
            <x-slot name="actions">
                <x-primary-button href="{{ route('admin.mentees.import.create') }}">
                    {{ __('Impor Mentee') }}
                </x-primary-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="{ selectedMentees: [], selectAll: false }">

                    @if(session('success'))
                        <div class="bg-teal-100 border border-teal-400 text-teal-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{!! session('success') !!}</span>
                        </div>
                    @endif
                    @if(session('warning'))
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{!! session('warning') !!}</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between mb-4">
                        <div x-show="selectedMentees.length > 0" class="flex items-center space-x-4">
                            <form id="bulk-delete-form" method="POST" action="{{ route('admin.mentees.bulkDestroy') }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <template x-for="id in selectedMentees" :key="id">
                                    <input type="hidden" name="ids[]" :value="id">
                                </template>
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        onclick="return confirm('APAKAH ANDA YAKIN? Tindakan ini akan menghapus ' + selectedMentees.length + ' data mentee yang dipilih dan tidak dapat dikembalikan.')">
                                    Hapus <span x-text="selectedMentees.length"></span> Mentee Terpilih
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.mentees.destroyAll') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 underline" onclick="return confirm('PERINGATAN KERAS! Tindakan ini akan menghapus SEMUA data mentee. Anda yakin?')">
                                    Hapus Semua Mentee
                                </button>
                            </form>
                        </div>
                        <div x-show="selectedMentees.length === 0" class="w-full">
                            <form method="GET" action="{{ route('admin.mentees.index') }}">
                                <div class="flex items-center">
                                    <input type="text" name="search" placeholder="Cari berdasarkan nama atau NPM..."
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
                            <thead>
                                <tr>
                                    <th scope="col" class="p-4">
                                        <input type="checkbox"
                                               class="rounded border-gray-300 text-brand-teal focus:ring-brand-teal"
                                               @click="selectAll = !selectAll; selectedMentees = selectAll ? {{ $mentees->pluck('id') }} : []">
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NPM</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fakultas</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mentees as $mentee)
                                    <tr class="@if($loop->even) bg-brand-mist @endif border-b border-gray-200 hover:bg-brand-sky/40"
                                        :class="{'bg-yellow-100': selectedMentees.includes({{ $mentee->id }})}">
                                        <td class="p-4">
                                            <input type="checkbox" x-model="selectedMentees" value="{{ $mentee->id }}"
                                                   class="rounded border-gray-300 text-brand-teal focus:ring-brand-teal">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $mentee->npm }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mentee->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $mentee->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $mentee->faculty->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">{{ $mentee->gender }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.mentees.show', $mentee) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                                            <form action="{{ route('admin.mentees.destroy', $mentee) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin menghapus mentee ini?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Belum ada data mentee.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $mentees->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
