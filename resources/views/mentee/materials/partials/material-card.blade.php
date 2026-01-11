<div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-shadow duration-200">
    <div>
        <div class="flex items-center mb-3">
            <svg class="h-6 w-6 text-brand-teal mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
            </svg>
            <h4 class="text-lg font-bold text-gray-800">{{ $material->title }}</h4>
        </div>
        <p class="text-sm text-gray-700 mb-4 flex-grow">{{ $material->description }}</p>
    </div>
    <div class="mt-4 flex flex-col space-y-2">
        @if($material->file_path)
            <a href="{{ Storage::url($material->file_path) }}" target="_blank" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-brand-teal hover:bg-brand-gold focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2">
                <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Unduh Materi ({{ pathinfo($material->file_path, PATHINFO_EXTENSION) }})
            </a>
        @endif
        @if($material->link)
            <a href="{{ $material->link }}" target="_blank" class="inline-flex items-center justify-center px-4 py-2 border border-brand-teal text-sm font-medium rounded-md shadow-sm text-brand-teal bg-white hover:bg-brand-teal hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-teal focus:ring-offset-2">
                <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
                Akses Link (Eksternal)
            </a>
        @endif
    </div>
</div>
