<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Option Model
 *
 * Model untuk pilihan jawaban soal (MODULE C #6: Ujian Akhir Mentoring)
 * Setiap soal ujian memiliki beberapa opsi jawaban (A, B, C, D, dll)
 * Salah satu opsi ditandai sebagai jawaban yang benar
 *
 * Attributes:
 * - question_id: Foreign key ke Question (soal mana yang punya opsi ini)
 * - option_text: Teks pilihan jawaban
 * - is_correct: Boolean flag apakah ini jawaban benar atau salah
 * - timestamps: created_at & updated_at otomatis
 *
 * @package App\Models
 */
class Option extends Model
{
    /**
     * Attributes yang bisa di-mass assign
     *
     * @var list<string>
     */
    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
    ];

    /**
     * Cast attributes ke tipe yang sesuai
     * is_correct: Cast ke boolean (1/0 -> true/false)
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * Opsi punya 1 Question
     * Opsi adalah pilihan jawaban untuk satu soal spesifik
     * Relasi: options.question_id -> questions.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
