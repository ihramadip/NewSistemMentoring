<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreExamRequest;
use App\Http\Requests\Admin\UpdateExamRequest;
use App\Models\Exam;
use App\Models\Level;
use App\Services\ExamService;

/**
 * ExamController
 *
 * Controller untuk manage ujian akhir (MODULE C #4: Final Exam)
 * Admin dapat create, update, delete ujian dan manage soal-soalnya
 *
 * Fitur:
 * - Index: list semua ujian dengan level & creator info, paginated 10
 * - Create: show form untuk create ujian baru, dropdown levels
 * - Store: save ujian baru via ExamService, validate via StoreExamRequest
 * - Show: display ujian detail dengan semua soal & options
 * - Edit: show form untuk edit ujian, dropdown levels, eager load questions
 * - Update: update ujian via ExamService, validate via UpdateExamRequest
 * - Delete: hapus ujian (dan soal-soalnya via cascade)
 *
 * Exam model menggunakan morphMany untuk polymorphic relationship:
 * - Exam.questions adalah morphMany('questions', 'questionable')
 * - Questions untuk ujian dapat di-reuse (polymorphic relation)
 *
 * Flow:
 * 1. Admin list ujian (index)
 * 2. Admin create ujian baru (create form)
 * 3. Admin input nama ujian & select level (store via service)
 * 4. Admin view ujian detail & manage soal-soal (show)
 * 5. Admin edit ujian info atau hapus
 * 6. System soft-delete atau cascade delete untuk soal-soal
 *
 * @package App\Http\Controllers\Admin
 */
class ExamController extends Controller
{
    /**
     * Service untuk handle exam business logic
     * Di-inject via constructor DI
     *
     * @var ExamService
     */
    protected $examService;

    /**
     * Constructor - Inject ExamService
     *
     * @param ExamService $examService Service untuk exam operations
     */
    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    /**
     * Menampilkan list semua ujian
     *
     * Proses:
     * 1. Query semua exams dengan eager load level & creator
     * 2. Paginate dengan 10 records per halaman
     * 3. Return view dengan data exams
     *
     * Eager loading:
     * - level: tingkat kesulitan ujian (Ibtida, Fasih, etc)
     * - creator: user (admin) yang create ujian
     * - Untuk mencegah N+1 query problem di view
     *
     * @return \Illuminate\View\View View list ujian dengan pagination
     */
    public function index()
    {
        // Query semua ujian dengan eager load level & creator, paginate 10
        $exams = Exam::with(['level', 'creator'])->paginate(10);

        // Return view dengan data exams
        return view('admin.exams.index', compact('exams'));
    }

    /**
     * Menampilkan form untuk create ujian baru
     *
     * Proses:
     * 1. Fetch semua levels dari database
     * 2. Return create view dengan levels dropdown
     *
     * Data:
     * - levels: list semua tingkat (Ibtida, Fasih, Hijaiyah, etc)
     * - Admin select 1 level saat create ujian
     *
     * @return \Illuminate\View\View View form create ujian
     */
    public function create()
    {
        // Fetch semua levels untuk dropdown
        $levels = Level::all();

        // Return create view dengan levels
        return view('admin.exams.create', compact('levels'));
    }

    /**
     * Menyimpan ujian baru ke database
     *
     * Proses:
     * 1. Validasi input via StoreExamRequest
     * 2. Call service->createExam() dengan validated data
     * 3. Service handle: create Exam record, set creator_id & level_id
     * 4. Redirect ke index dengan success message
     *
     * Validasi (StoreExamRequest):
     * - name: required, string, unique ujian names
     * - level_id: required, exists in levels table
     * - description: optional, string
     *
     * @param StoreExamRequest $request Form request dengan validasi
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function store(StoreExamRequest $request)
    {
        // Call service untuk create ujian
        // Service return: newly created Exam model
        $exam = $this->examService->createExam($request->validated());

        // Redirect ke index dengan success message
        return redirect()->route('admin.exams.index')
                        ->with('success', 'Ujian "' . $exam->name . '" berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail ujian beserta semua soal & options
     *
     * Proses:
     * 1. Exam di-resolve via route model binding
     * 2. Eager load questions & options (polymorphic morphMany)
     * 3. Return view dengan ujian detail
     *
     * Data structure:
     * - exam.questions (polymorphic, morphMany)
     * - questions.options (hasMany)
     * - questions.is_correct flag untuk identify jawaban benar
     *
     * Eager loading:
     * - Fetch questions via morphMany relation (questionable_id & questionable_type)
     * - Fetch options untuk setiap question
     * - Mencegah N+1 queries di view
     *
     * @param Exam $exam Exam model via route binding
     * @return \Illuminate\View\View View ujian detail dengan soal-soal
     */
    public function show(Exam $exam)
    {
        // Eager load questions & options untuk display detail ujian
        // polymorphic relation: exam.questions -> questions.options
        $exam->load(['questions.options']);

        // Return view dengan ujian detail
        return view('admin.exams.show', compact('exam'));
    }

    /**
     * Menampilkan form untuk edit ujian
     *
     * Proses:
     * 1. Exam di-resolve via route model binding
     * 2. Eager load questions & options
     * 3. Fetch semua levels untuk dropdown
     * 4. Return edit view dengan data ujian & levels
     *
     * Data:
     * - exam: current ujian untuk populate form
     * - exam.questions: list soal-soal ujian
     * - levels: dropdown untuk change level
     *
     * Eager loading:
     * - questions.options untuk display di view
     * - Untuk edit form, user dapat lihat soal-soal yang ada
     *
     * @param Exam $exam Exam model via route binding
     * @return \Illuminate\View\View View form edit ujian
     */
    public function edit(Exam $exam)
    {
        // Eager load questions & options untuk display di form
        $exam->load(['questions.options']);

        // Fetch semua levels untuk dropdown
        $levels = Level::all();

        // Return edit view dengan ujian & levels
        return view('admin.exams.edit', compact('exam', 'levels'));
    }

    /**
     * Memperbarui ujian di database
     *
     * Proses:
     * 1. Exam di-resolve via route model binding
     * 2. Validasi input via UpdateExamRequest
     * 3. Call service->updateExam() dengan ujian & validated data
     * 4. Service handle: update Exam record (name, level_id, description)
     * 5. Redirect ke index dengan success message
     *
     * Validasi (UpdateExamRequest):
     * - name: required, string, unique (except current ujian)
     * - level_id: required, exists in levels table
     * - description: optional, string
     *
     * Note:
     * - Service hanya update exam info, tidak touch soal-soal
     * - Soal-soal di-manage di QuestionController
     *
     * @param UpdateExamRequest $request Form request dengan validasi
     * @param Exam $exam Exam model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function update(UpdateExamRequest $request, Exam $exam)
    {
        // Call service untuk update ujian
        // Service handle: update name, level_id, description
        $this->examService->updateExam($exam, $request->validated());

        // Redirect ke index dengan success message
        return redirect()->route('admin.exams.index')
                        ->with('success', 'Ujian "' . $exam->name . '" berhasil diperbarui.');
    }

    /**
     * Menghapus ujian dari database
     *
     * Proses:
     * 1. Exam di-resolve via route model binding
     * 2. Call service->deleteExam() untuk delete operation
     * 3. Service handle: cascade delete questions & options (via morphMany)
     * 4. Service handle: cascade delete exam_submissions (jika ada)
     * 5. Redirect ke index dengan success message
     *
     * Cascade deletion:
     * - Exam delete -> morphMany questions soft delete/hard delete
     * - Questions delete -> hasMany options hard delete
     * - ExamSubmission -> delete semua submission records
     *
     * Note:
     * - Jika sudah ada exam_submissions, consider soft delete ujian
     * - Atau implement history/archive logic di service
     *
     * @param Exam $exam Exam model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function destroy(Exam $exam)
    {
        // Call service untuk delete ujian
        // Service handle: cascade delete questions, options, submissions
        $this->examService->deleteExam($exam);

        // Redirect ke index dengan success message
        return redirect()->route('admin.exams.index')
                        ->with('success', 'Ujian "' . $exam->name . '" berhasil dihapus.');
    }
}
