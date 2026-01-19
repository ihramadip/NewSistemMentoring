<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ExamSubmission Model
 *
 * Model untuk submisi/jawaban ujian akhir mentee (MODULE C #6: Ujian Akhir Mentoring)
 * Satu submission adalah record saat mentee mengerjakan satu ujian
 * Submission menyimpan jawaban mentee & score dari admin
 *
 * Attributes:
 * - mentee_id: Foreign key ke User (mentee yang mengerjakan ujian)
 * - exam_id: Foreign key ke Exam (ujian mana yang dikerjakan)
 * - submitted_at: Tanggal & waktu mentee submit jawaban
 * - total_score: Nilai akhir ujian (0-100, diisi oleh admin saat grading)
 * - status: Status submission (submitted, graded)
 * - timestamps: created_at & updated_at otomatis
 *
 * @package App\Models
 */
class ExamSubmission extends Model
{
    /**
     * Attributes yang bisa di-mass assign
     *
     * @var list<string>
     */
    protected $fillable = [
        'mentee_id',
        'exam_id',
        'submitted_at',
        'total_score',
        'status',
    ];

    /**
     * Cast attributes ke tipe yang sesuai
     * submitted_at: Convert ke DateTime object
     *
     * @var array<string, string>
     */
    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    /**
     * Submission punya 1 Mentee (User dengan role=mentee)
     * Mentee bisa submit multiple exams, tapi 1 submission = 1 exam
     * Relasi: exam_submissions.mentee_id -> users.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mentee()
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }

    /**
     * Submission punya 1 Exam (ujian yang dikerjakan)
     * Relasi: exam_submissions.exam_id -> exams.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Submission punya banyak SubmissionAnswer
     * Setiap answer adalah jawaban mentee untuk 1 soal
     * Relasi: exam_submissions.id -> submission_answers.exam_submission_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers()
    {
        return $this->hasMany(SubmissionAnswer::class);
    }
}
