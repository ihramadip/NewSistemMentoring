<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ProgressReport Model
 *
 * Model untuk catatan progres mentee per sesi mentoring (MODULE C #1: Mentoring Wajib)
 * Mentor mengisi progress report setelah setiap sesi untuk dokumentasi perkembangan mentee
 *
 * Attributes:
 * - session_id: Foreign key ke Session (sesi mentoring mana)
 * - mentee_id: Foreign key ke User (mentee mana yang dimonitor)
 * - score: Nilai/skor mentee di sesi ini (0-100)
 * - reading_notes: Catatan tentang bacaan mentee (intonasi, tajwid, dll)
 * - general_notes: Catatan umum/observasi lain tentang mentee
 * - timestamps: created_at & updated_at otomatis
 *
 * @package App\Models
 */
class ProgressReport extends Model
{
    /**
     * Attributes yang bisa di-mass assign
     *
     * @var list<string>
     */
    protected $fillable = [
        'session_id',
        'mentee_id',
        'score',
        'reading_notes',
        'general_notes',
    ];

    /**
     * Progress report punya 1 Session
     * Report adalah catatan untuk satu sesi spesifik
     * Relasi: progress_reports.session_id -> mentoring_sessions.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Progress report punya 1 Mentee (User dengan role=mentee)
     * Report mencatat perkembangan mentee spesifik
     * Relasi: progress_reports.mentee_id -> users.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mentee()
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }
}
