<!-- resources/views/components/page-header.blade.php -->
@props(['title', 'icon' => null, 'subtitle' => null, 'actions' => null]) {{-- Add 'actions' to props --}}

<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        <div class="flex items-center">
            @if($icon)
                <div class="mr-4 text-brand-teal p-2 rounded-full bg-brand-teal/10">
                    {{ $icon }}
                </div>
            @endif
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ $title }}
                </h2>
                @if($subtitle)
                    <p class="mt-1 text-md text-gray-600">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        @if ($actions) {{-- Render actions slot if it exists --}}
            <div class="ml-auto flex items-center space-x-2">
                {{ $actions }}
            </div>
        @endif
    </div>
</header>
