<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Question;

/**
 * QuestionController
 *
 * Controller untuk manage soal ujian (MODULE C #6: Ujian Akhir Mentoring)
 * Admin dapat create, read, update, delete soal untuk ujian tertentu
 * Soal dapat berupa pilihan ganda, esai, atau respon audio
 *
 * Fitur:
 * - Index: list semua soal untuk ujian tertentu dengan options (paginate 10)
 * - Create: show form untuk create soal baru, input text, type, score, options
 * - Store: save soal baru ke ujian, validate required fields & question type rules
 * - Edit: show form untuk edit soal existing dengan options
 * - Update: update soal info & manage options (create/update/delete)
 * - Delete: hapus soal dari ujian (cascade delete options)
 *
 * Data structure:
 * - Question: question_text, question_type, score_value, questionable_id, questionable_type
 * - Options: option_text, is_correct (for multiple choice questions)
 * - Relationship: belongsTo Exam via polymorphic relation (questionable)
 *
 * Question types:
 * - multiple_choice: soal pilihan ganda dengan beberapa opsi jawaban
 * - essay: soal esai yang perlu dikoreksi manual
 * - audio_response: soal dengan respon audio dari mentee
 *
 * Multiple choice validation:
 * - Minimal 1 option harus ada untuk soal pilihan ganda
 * - Minimal 1 option harus ditandai sebagai benar (is_correct = true)
 * - Validasi dilakukan di store & update method
 *
 * Options management:
 * - Create: tambah opsi baru saat create soal
 * - Update: update opsi yang sudah ada (by id) saat edit soal
 * - Delete: hapus opsi yang tidak lagi ada di form edit
 * - Sync: hanya opsi yang ada di form yang dipertahankan
 *
 * Flow:
 * 1. Admin pilih ujian dan lihat list soal (index)
 * 2. Admin create soal baru (create form, input text, type, score, options)
 * 3. Admin edit soal existing (edit form, update text, type, score, options)
 * 4. Admin hapus soal jika tidak relevan (delete)
 *
 * @package App\Http\Controllers\Admin
 */
class QuestionController extends Controller
{
    /**
     * Menampilkan list semua soal untuk ujian tertentu
     *
     * Proses:
     * 1. Exam di-resolve via route model binding
     * 2. Query soal-soal milik ujian tersebut dengan eager load options
     * 3. Paginate dengan 10 records per halaman
     * 4. Return view dengan exam & questions
     *
     * Eager loading:
     * - options: pilihan jawaban untuk soal pilihan ganda
     * - Untuk mencegah N+1 query problem di view (display options)
     *
     * Polymorphic relation:
     * - Questions menggunakan relation polymorphic ke Exam
     * - exam->questions() mengembalikan soal-soal milik ujian ini
     *
     * @param Exam $exam Exam model via route binding
     * @return \Illuminate\View\View View list soal untuk ujian
     */
    public function index(Exam $exam)
    {
        // Query soal-soal milik ujian dengan eager load options, paginate 10
        $questions = $exam->questions()->with('options')->paginate(10);

        // Return view dengan exam & questions
        return view('admin.questions.index', compact('exam', 'questions'));
    }

    /**
     * Menampilkan form untuk create soal baru
     *
     * Proses:
     * 1. Exam di-resolve via route model binding
     * 2. Define available question types untuk dropdown
     * 3. Return create view dengan exam & question types
     *
     * Question types:
     * - multiple_choice: 'Pilihan Ganda' - soal dengan opsi jawaban
     * - essay: 'Esai' - soal jawaban panjang
     * - audio_response: 'Respon Audio' - soal dengan respon audio
     *
     * @param Exam $exam Exam model via route binding
     * @return \Illuminate\View\View View form create soal
     */
    public function create(Exam $exam)
    {
        // Define question types untuk dropdown
        $questionTypes = [
            'multiple_choice' => 'Pilihan Ganda',
            'essay' => 'Esai',
            'audio_response' => 'Respon Audio',
        ];

        // Return create view dengan exam & question types
        return view('admin.questions.create', compact('exam', 'questionTypes'));
    }

    /**
     * Menyimpan soal baru ke ujian
     *
     * Proses:
     * 1. Validasi input:
     *    - question_text: required, string
     *    - question_type: required, in: multiple_choice, essay, audio_response
     *    - score_value: required, integer, min 1
     *    - options: array (for multiple choice)
     *    - options.*.text: required_with:options, string, max 255
     *    - options.*.is_correct: boolean
     * 2. Validate multiple choice rules:
     *    - Minimal 1 option harus ada jika question_type = multiple_choice
     *    - Minimal 1 option harus benar jika question_type = multiple_choice
     * 3. Create Question record ke ujian (via relationship)
     * 4. Jika multiple choice, create options records
     * 5. Redirect ke edit ujian dengan success message
     *
     * Validasi (inline):
     * - question_text: required, string
     * - question_type: required, in:multiple_choice,essay,audio_response
     * - score_value: required, integer, min:1
     * - options: array
     * - options.*.text: required_with:options, string, max:255
     * - options.*.is_correct: boolean
     *
     * Multiple choice validation:
     * - Cek empty options jika type = multiple_choice
     * - Cek minimal 1 option is_correct jika type = multiple_choice
     * - Gunakan back() dengan errors jika validasi gagal
     *
     * @param Request $request Form request dengan soal info
     * @param Exam $exam Exam model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke edit ujian dengan success
     */
    public function store(Request $request, Exam $exam)
    {
        // Validasi input
        $validatedData = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,essay,audio_response',
            'score_value' => 'required|integer|min:1',
            'options' => 'array', // For multiple choice
            'options.*.text' => 'required_with:options|string|max:255',
            'options.*.is_correct' => 'boolean',
        ]);

        // Validasi rules untuk multiple choice
        if ($validatedData['question_type'] === 'multiple_choice') {
            if (empty($validatedData['options'])) {
                return back()->withErrors(['options' => 'Pertanyaan pilihan ganda memerlukan setidaknya satu opsi.'])->withInput();
            }
            $hasCorrectOption = collect($validatedData['options'])->pluck('is_correct')->filter()->isNotEmpty();
            if (!$hasCorrectOption) {
                return back()->withErrors(['options' => 'Pertanyaan pilihan ganda memerlukan setidaknya satu opsi yang benar.'])->withInput();
            }
        }

        // Create new question ke ujian
        $question = $exam->questions()->create([
            'question_text' => $validatedData['question_text'],
            'question_type' => $validatedData['question_type'],
            'score_value' => $validatedData['score_value'],
        ]);

        // Jika multiple choice, create options
        if ($validatedData['question_type'] === 'multiple_choice' && isset($validatedData['options'])) {
            foreach ($validatedData['options'] as $optionData) {
                $question->options()->create([
                    'option_text' => $optionData['text'],
                    'is_correct' => $optionData['is_correct'] ?? false,
                ]);
            }
        }

        // Redirect ke edit ujian dengan success message
        return redirect()->route('admin.exams.edit', $exam)
                         ->with('success', 'Pertanyaan berhasil ditambahkan ke ujian ' . $exam->name . '.');
    }

    /**
     * Menampilkan detail soal (belum diimplementasi)
     *
     * Proses:
     * 1. Saat ini tidak ada implementasi untuk show method
     * 2. Biasanya untuk detail soal, admin akan edit langsung dari list
     *
     * Note:
     * - Fungsi ini saat ini tidak digunakan
     * - Untuk detail, admin biasanya edit dari list soal
     * - Future enhancement: buat show view untuk detail soal
     *
     * @param string $id ID soal (dilewatkan sebagai string karena tidak digunakan)
     * @return void
     */
    public function show(string $id)
    {
        // Method ini tidak diimplementasi karena soal detail biasanya
        // ditampilkan di halaman edit ujian atau edit soal
    }

    /**
     * Menampilkan form untuk edit soal existing
     *
     * Proses:
     * 1. Exam & Question di-resolve via route model binding
     * 2. Eager load options untuk soal (untuk populate form)
     * 3. Define available question types untuk dropdown
     * 4. Return edit view dengan exam, question & question types
     *
     * Data:
     * - exam: ujian yang punya soal ini
     * - question: current question untuk populate form (sudah dengan options)
     * - questionTypes: list available question types
     *
     * @param Exam $exam Exam model via route binding
     * @param Question $question Question model via route binding
     * @return \Illuminate\View\View View form edit soal
     */
    public function edit(Exam $exam, Question $question)
    {
        // Eager load options untuk editing
        $question->load('options');

        // Define question types untuk dropdown
        $questionTypes = [
            'multiple_choice' => 'Pilihan Ganda',
            'essay' => 'Esai',
            'audio_response' => 'Respon Audio',
        ];

        // Return edit view dengan exam, question & question types
        return view('admin.questions.edit', compact('exam', 'question', 'questionTypes'));
    }

    /**
     * Memperbarui soal di database
     *
     * Proses:
     * 1. Validasi input (sama seperti store):
     *    - question_text: required, string
     *    - question_type: required, in: multiple_choice, essay, audio_response
     *    - score_value: required, integer, min 1
     *    - options: array (for multiple choice)
     *    - options.*.text: required_with:options, string, max 255
     *    - options.*.is_correct: boolean
     * 2. Validate multiple choice rules (sama seperti store)
     * 3. Update Question record: text, type, score
     * 4. Handle options update (create/update/delete):
     *    - Delete options yang tidak lagi ada di form
     *    - Update options yang sudah ada (by id)
     *    - Create options baru (tanpa id)
     *    - Jika bukan multiple choice, delete semua options
     * 5. Redirect ke edit ujian dengan success message
     *
     * Validasi (inline):
     * - question_text: required, string
     * - question_type: required, in:multiple_choice,essay,audio_response
     * - score_value: required, integer, min:1
     * - options: array
     * - options.*.text: required_with:options, string, max:255
     * - options.*.is_correct: boolean
     *
     * Options management:
     * - Delete old options: whereNotIn('id', $existingOptionIds)
     * - Update existing: find by id, update text & is_correct
     * - Create new: create without id
     * - Delete all if not multiple_choice: $question->options()->delete()
     *
     * @param Request $request Form request dengan soal info
     * @param Exam $exam Exam model via route binding
     * @param Question $question Question model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke edit ujian dengan success
     */
    public function update(Request $request, Exam $exam, Question $question)
    {
        // Validasi input
        $validatedData = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,essay,audio_response',
            'score_value' => 'required|integer|min:1',
            'options' => 'array', // For multiple choice
            'options.*.text' => 'required_with:options|string|max:255',
            'options.*.is_correct' => 'boolean',
        ]);

        // Validasi rules untuk multiple choice
        if ($validatedData['question_type'] === 'multiple_choice') {
            if (empty($validatedData['options'])) {
                return back()->withErrors(['options' => 'Pertanyaan pilihan ganda memerlukan setidaknya satu opsi.'])->withInput();
            }
            $hasCorrectOption = collect($validatedData['options'])->pluck('is_correct')->filter()->isNotEmpty();
            if (!$hasCorrectOption) {
                return back()->withErrors(['options' => 'Pertanyaan pilihan ganda memerlukan setidaknya satu opsi yang benar.'])->withInput();
            }
        }

        // Update question info
        $question->update([
            'question_text' => $validatedData['question_text'],
            'question_type' => $validatedData['question_type'],
            'score_value' => $validatedData['score_value'],
        ]);

        // Handle options update
        if ($validatedData['question_type'] === 'multiple_choice' && isset($validatedData['options'])) {
            // Delete old options yang tidak lagi ada di form
            $existingOptionIds = collect($validatedData['options'])->filter(fn($opt) => isset($opt['id']))->pluck('id');
            $question->options()->whereNotIn('id', $existingOptionIds)->delete();

            foreach ($validatedData['options'] as $optionData) {
                if (isset($optionData['id'])) {
                    // Update existing option
                    $option = $question->options()->find($optionData['id']);
                    if ($option) {
                        $option->update([
                            'option_text' => $optionData['text'],
                            'is_correct' => $optionData['is_correct'] ?? false,
                        ]);
                    }
                } else {
                    // Create new option
                    $question->options()->create([
                        'option_text' => $optionData['text'],
                        'is_correct' => $optionData['is_correct'] ?? false,
                    ]);
                }
            }
        } else {
            // Jika bukan multiple choice, delete semua options
            $question->options()->delete();
        }

        // Redirect ke edit ujian dengan success message
        return redirect()->route('admin.exams.edit', $exam)
                         ->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    /**
     * Menghapus soal dari ujian
     *
     * Proses:
     * 1. Exam & Question di-resolve via route model binding
     * 2. Delete Question record (cascade delete options)
     * 3. Redirect ke edit ujian dengan success message
     *
     * Cascade deletion:
     * - Question delete -> options delete (via relationship)
     * - Hanya soal & opsi yang dihapus, tidak mempengaruhi ujian atau user
     *
     * WARNING:
     * - Soal akan hilang sepenuhnya dari ujian beserta semua opsi
     * - Tidak ada soft delete atau archive mechanism saat ini
     * - Pastikan benar-benar ingin hapus sebelum konfirmasi
     *
     * @param Exam $exam Exam model via route binding
     * @param Question $question Question model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke edit ujian dengan success
     */
    public function destroy(Exam $exam, Question $question)
    {
        // Delete question record (cascade delete options)
        $question->delete();

        // Redirect ke edit ujian dengan success message
        return redirect()->route('admin.exams.edit', $exam)
                         ->with('success', 'Pertanyaan berhasil dihapus dari ujian ' . $exam->name . '.');
    }
}
