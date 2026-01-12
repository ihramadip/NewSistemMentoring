<x-app-layout>
    <x-slot name="header">
        <x-page-header title="{{ __('Detail Mentee') }}" subtitle="Lihat detail informasi lengkap mentee {{ $mentee->name }}.">
            <x-slot name="icon">
                <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-4.67c.12-.313.253-.617.4-1.022a4.125 4.125 0 00-7.533-2.493c-3.693 0-6.105 2.818-6.105 6.375a6.375 6.375 0 004.121 5.952v-.003c1.113 0 2.16-.285 3.07-.786z" />
                </svg>
            </x-slot>
            <x-slot name="actions">
                <x-primary-button href="{{ route('admin.mentees.index') }}">
                    {{ __('Kembali ke Daftar Mentee') }}
                </x-primary-button>
            </x-slot>
        </x-page-header>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Informasi Mentee</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Nama Lengkap</p>
                                <p class="text-base text-gray-900">{{ $mentee->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">NPM</p>
                                <p class="text-base text-gray-900">{{ $mentee->npm }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Email</p>
                                <p class="text-base text-gray-900">{{ $mentee->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Fakultas</p>
                                <p class="text-base text-gray-900">{{ $mentee->faculty->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Jenis Kelamin</p>
                                <p class="text-base text-gray-900 capitalize">{{ $mentee->gender }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Role</p>
                                <p class="text-base text-gray-900">{{ $mentee->role->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Tanggal Terdaftar</p>
                                <p class="text-base text-gray-900">{{ $mentee->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
