@if(session('success'))
    <div class="bg-teal-100 border border-teal-400 text-teal-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

<div class="space-y-6">
    @forelse ($questions as $question)
        <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-lg font-bold text-gray-800">{{ $loop->iteration }}. {{ $question->question_text }}</h4>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ $question->score_value }} Poin</span>
                </div>
            </div>
            
            @if($question->question_type === 'multiple_choice' && $question->options->isNotEmpty())
                <ul class="mt-4 space-y-2 text-sm text-gray-700">
                    @foreach($question->options as $option)
                        <li class="flex items-start">
                            <span class="mr-2 {{ $option->is_correct ? 'text-green-600' : 'text-gray-500' }}">
                                @if($option->is_correct)
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </span>
                            {{ $option->option_text }}
                        </li>
                    @endforeach
                </ul>
            @endif
            <div class="mt-4 flex justify-end items-center space-x-3">
                <a href="{{ route('admin.exams.questions.edit', [$exam, $question]) }}" class="text-sm text-brand-teal hover:text-brand-gold">Edit</a>
                <form action="{{ route('admin.exams.questions.destroy', [$exam, $question]) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:text-red-800" onclick="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')">Hapus</button>
                </form>
            </div>
        </div>
    @empty
        <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6 text-center">
            <p class="text-sm text-gray-500">Belum ada pertanyaan untuk ujian ini.</p>
        </div>
    @endforelse
</div>

@if ($questions instanceof \Illuminate\Pagination\AbstractPaginator)
    <div class="mt-4">
        {{ $questions->links() }}
    </div>
@endif
