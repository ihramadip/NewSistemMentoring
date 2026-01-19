<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * AdditionalSession Model
 *
 * Model untuk sesi mentoring tambahan (MODULE C #2: Mentoring Tambahan 21 Pertemuan)
 * Mentee dapat mendaftar untuk sesi tambahan beyond 7 sesi wajib
 * Setiap sesi tambahan memerlukan bukti (foto/video/audio)
 *
 * Attributes:
 * - mentee_id: Foreign key ke User (mentee yang mengikuti sesi)
 * - mentoring_group_id: Foreign key ke MentoringGroup (grup yang menaungi)
 * - topic: Topik/materi sesi tambahan
 * - date: Tanggal & waktu sesi
 * - status: Status sesi (pending, approved, rejected, completed)
 * - proof_path: Path file bukti (foto/video/audio rekaman)
 * - timestamps: created_at & updated_at otomatis
 *
 * @package App\Models
 */
class AdditionalSession extends Model
{
    use HasFactory;

    /**
     * Attributes yang bisa di-mass assign
     *
     * @var list<string>
     */
    protected $fillable = [
        'mentee_id',
        'mentoring_group_id',
        'topic',
        'date',
        'status',
        'proof_path',
    ];

    /**
     * Sesi tambahan punya 1 Mentee
     * Mentee adalah yang mengikuti sesi tambahan
     * Relasi: additional_sessions.mentee_id -> users.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mentee()
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }

    /**
     * Sesi tambahan punya 1 MentoringGroup
     * Grup menaungi & track sesi tambahan mentee-nya
     * Relasi: additional_sessions.mentoring_group_id -> mentoring_groups.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mentoringGroup()
    {
        return $this->belongsTo(MentoringGroup::class);
    }
}