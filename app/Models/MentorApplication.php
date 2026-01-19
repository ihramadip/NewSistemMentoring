<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * MentorApplication Model
 *
 * Model untuk menyimpan data pendaftaran calon pementor (MODULE A #1: Oprec Pementor)
 *
 * Attributes:
 * - user_id: Foreign key ke User yang mendaftar
 * - cv_path: Path file CV (lokasi penyimpanan di storage)
 * - recording_path: Path file rekaman bacaan Al-Qur'an (audio)
 * - btaq_history: Riwayat BTAQ (Baca Tulis Al-Qur'an) calon mentor
 * - status: Status aplikasi (pending, approved, rejected)
 * - notes_from_reviewer: Catatan/feedback dari reviewer (admin)
 * - timestamps: created_at & updated_at otomatis
 *
 * @package App\Models
 */
class MentorApplication extends Model
{
    /**
     * Attributes yang bisa di-mass assign
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'cv_path',
        'recording_path',
        'btaq_history',
        'status',
        'notes_from_reviewer',
    ];

    /**
     * Aplikasi punya 1 User (relasi many-to-one)
     * Satu user hanya bisa 1 aplikasi mentor (di validate di validation rule)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
