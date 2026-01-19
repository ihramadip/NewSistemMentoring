<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Attendance Model
 *
 * Model untuk pencatatan kehadiran mentee di sesi mentoring (MODULE C #1: Mentoring Wajib)
 * Setiap mentee memiliki 1 record attendance per sesi mentoring
 *
 * Attributes:
 * - session_id: Foreign key ke Session (sesi mentoring mana)
 * - mentee_id: Foreign key ke User (mentee mana)
 * - status: Status kehadiran (present, absent, sick, permit)
 * - notes: Catatan/alasan jika tidak hadir atau keterangan lain
 * - timestamps: created_at & updated_at otomatis
 *
 * @package App\Models
 */
class Attendance extends Model
{
    /**
     * Attributes yang bisa di-mass assign
     *
     * @var list<string>
     */
    protected $fillable = [
        'session_id',
        'mentee_id',
        'status',
        'notes',
    ];

    /**
     * Attendance punya 1 Session
     * Attendance adalah record kehadiran di satu sesi spesifik
     * Relasi: attendances.session_id -> mentoring_sessions.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Attendance punya 1 Mentee (User dengan role=mentee)
     * Mencatat mentee mana yang hadir/tidak hadir
     * Relasi: attendances.mentee_id -> users.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mentee()
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }
}
