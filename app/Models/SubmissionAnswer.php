<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * SubmissionAnswer Model
 *
 * Model untuk jawaban mentee per soal di exam submission (MODULE C #6: Ujian Akhir Mentoring)
 * Setiap submission memiliki multiple answers untuk setiap soal di exam
 * Menyimpan pilihan jawaban mentee & score untuk soal tersebut
 *
 * Attributes:
 * - exam_submission_id: Foreign key ke ExamSubmission (submission mana)
 * - question_id: Foreign key ke Question (soal mana yang dijawab)
 * - chosen_option_id: Foreign key ke Option (opsi mana yang dipilih mentee)
 * - answer_text: Text jawaban (untuk soal essay, jika ada)
 * - score: Nilai/skor untuk jawaban ini (0 atau full point jika benar)
 * - timestamps: created_at & updated_at otomatis
 *
 * @package App\Models
 */
class SubmissionAnswer extends Model
{
    /**
     * Attributes yang bisa di-mass assign
     *
     * @var list<string>
     */
    protected $fillable = [
        'exam_submission_id',
        'question_id',
        'chosen_option_id',
        'answer_text',
        'score',
    ];

    /**
     * Jawaban punya 1 ExamSubmission
     * Jawaban adalah bagian dari satu submission spesifik
     * Relasi: submission_answers.exam_submission_id -> exam_submissions.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function examSubmission()
    {
        return $this->belongsTo(ExamSubmission::class);
    }

    /**
     * Jawaban punya 1 Question
     * Jawaban adalah respons untuk satu soal spesifik
     * Relasi: submission_answers.question_id -> questions.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Jawaban punya 1 chosen Option
     * Option adalah pilihan yang dipilih mentee (untuk multiple choice)
     * Relasi: submission_answers.chosen_option_id -> options.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chosenOption()
    {
        return $this->belongsTo(Option::class, 'chosen_option_id');
    }
}
