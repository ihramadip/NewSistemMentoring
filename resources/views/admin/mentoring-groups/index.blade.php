<x-app-layout>
    <x-slot name="header">
        <x-page-header
            title="{{ __('Manajemen Kelompok Mentoring') }}"
            subtitle="Kelola daftar kelompok mentoring, mentor, dan anggotanya."
        >
            <x-slot name="icon">
                <svg
                    class="h-8 w-8"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M18 18.72a9.094 9.094 0 003.741-.479
                           3 3 0 00-4.682-2.72m-7.5-2.962
                           A3 3 0 0115 9.185a3 3 0 01-4.5 2.72
                           m-7.5-2.962a3 3 0 00-4.682 2.72
                           8.982 8.982 0 003.741.479M21 12
                           a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
            </x-slot>

            <x-slot name="actions">
                <x-primary-button href="{{ route('admin.mentoring-groups.create') }}">
                    {{ __('Tambah Kelompok') }}
                </x-primary-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                        <div
                            class="bg-teal-100 border border-teal-400 text-teal-700
                                   px-4 py-3 rounded relative mb-4"
                            role="alert"
                        >
                            <span class="block sm:inline">
                                {{ session('success') }}
                            </span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Kelompok
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Mentor
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Level
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jadwal
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($mentoringGroups as $group)
                                    <tr class="hover:bg-brand-mist/40">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $group->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $group->mentor->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $group->level->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $group->schedule_info }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-medium">
                                            <a
                                                href="{{ route('admin.mentoring-groups.edit', $group) }}"
                                                class="text-brand-teal hover:text-brand-gold mr-3"
                                            >
                                                Edit
                                            </a>

                                            <form
                                                action="{{ route('admin.mentoring-groups.destroy', $group) }}"
                                                method="POST"
                                                class="inline-block"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus kelompok ini?')"
                                                >
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td
                                            colspan="5"
                                            class="px-6 py-4 text-sm text-gray-500 text-center"
                                        >
                                            Tidak ada kelompok mentoring yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $mentoringGroups->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
