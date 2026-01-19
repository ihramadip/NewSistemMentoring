<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Question Model
 *
 * Model untuk soal ujian (MODULE C #6: Ujian Akhir Mentoring)
 * Question dapat dimiliki oleh Exam atau PlacementTestDefinition (polymorphic)
 * Setiap soal memiliki beberapa pilihan jawaban (options)
 *
 * Attributes:
 * - questionable_id: ID dari parent model (Exam atau PlacementTestDefinition)
 * - questionable_type: Tipe parent model (App\Models\Exam atau App\Models\PlacementTestDefinition)
 * - question_text: Isi/teks soal
 * - question_type: Tipe soal (multiple_choice, essay, true_false, dll)
 * - score_value: Nilai/bobot soal jika dijawab benar
 * - timestamps: created_at & updated_at otomatis
 *
 * Polymorphic Relation:
 * - questionable dapat berupa Exam atau PlacementTestDefinition
 * - Memungkinkan reuse question logic untuk multiple parent models
 *
 * @package App\Models
 */
class Question extends Model
{
    /**
     * Attributes yang bisa di-mass assign
     *
     * @var list<string>
     */
    protected $fillable = [
        'questionable_id',
        'questionable_type',
        'question_text',
        'question_type',
        'score_value',
    ];

    /**
     * Soal dimiliki oleh parent model secara polymorphic
     * Parent dapat berupa Exam atau PlacementTestDefinition
     * Relasi polymorphic: questions.questionable_id & questions.questionable_type
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function questionable()
    {
        return $this->morphTo();
    }

    /**
     * Soal punya banyak Option (pilihan jawaban)
     * Setiap soal multiple choice memiliki beberapa opsi (A, B, C, D, dll)
     * Relasi: questions.id -> options.question_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function options()
    {
        return $this->hasMany(Option::class);
    }
}
