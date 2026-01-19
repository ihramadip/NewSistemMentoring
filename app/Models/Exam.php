<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Exam Model
 *
 * Model untuk ujian akhir mentoring (MODULE C #6: Ujian Akhir Mentoring)
 * Ujian dibuat per level (Fasih, Ibtida, Hijaiyah 1, Hijaiyah 2)
 * Ujian berisi beberapa soal pilihan ganda yang dijawab oleh mentee
 *
 * Attributes:
 * - name: Nama ujian (misal: "Ujian Akhir Level Fasih")
 * - description: Deskripsi/keterangan ujian
 * - level_id: Foreign key ke Level (ujian untuk level apa)
 * - duration_minutes: Durasi mengerjakan ujian (dalam menit)
 * - published_at: Tanggal ujian di-publish (untuk validasi saat mentee ambil ujian)
 * - created_by: Foreign key ke User (admin yang membuat ujian)
 * - timestamps: created_at & updated_at otomatis
 *
 * @package App\Models
 */
class Exam extends Model
{
    /**
     * Attributes yang bisa di-mass assign
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'level_id',
        'duration_minutes',
        'published_at',
        'created_by',
    ];

    /**
     * Cast attributes ke tipe yang sesuai
     * published_at: Convert ke DateTime object
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Ujian punya 1 Level
     * Ujian dibuat spesifik untuk satu level mentoring
     * Relasi: exams.level_id -> levels.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * Ujian dibuat oleh 1 User (Admin)
     * Mencatat siapa pembuat ujian untuk audit trail
     * Relasi: exams.created_by -> users.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Ujian punya banyak Question (polymorphic relation)
     * Question dapat dimiliki oleh Exam atau PlacementTestDefinition
     * Relasi: questions.questionable_id & questions.questionable_type
     * Tipe: 'App\Models\Exam'
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function questions()
    {
        return $this->morphMany(Question::class, 'questionable');
    }
}
