<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Session Model (Mentoring Session)
 *
 * Model untuk sesi/pertemuan mentoring (MODULE C #1: Mentoring Sessions)
 * Satu kelompok mentoring memiliki 7 sesi wajib + 21 sesi tambahan (total 28)
 *
 * Attributes:
 * - mentoring_group_id: Foreign key ke MentoringGroup (kelompok yg punya session)
 * - session_number: Nomor urut sesi (1-28)
 * - date: Tanggal & waktu sesi
 * - title: Judul sesi (misal: "Sesi 1 - Al-Fatihah")
 * - description: Deskripsi/keterangan materi sesi
 * - timestamps: created_at & updated_at otomatis
 *
 * Database table: mentoring_sessions (not 'sessions' karena reserved)
 *
 * @package App\Models
 */
class Session extends Model
{
    /**
     * Explicit table name untuk menghindari konflik dengan reserved word 'sessions'
     *
     * @var string
     */
    protected $table = 'mentoring_sessions';

    /**
     * Attributes yang bisa di-mass assign
     *
     * @var list<string>
     */
    protected $fillable = [
        'mentoring_group_id',
        'session_number',
        'date',
        'title',
        'description',
    ];

    /**
     * Cast attributes ke tipe yang sesuai
     * date: Convert ke DateTime object untuk kemudahan handling
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * Sesi punya 1 MentoringGroup
     * Satu group memiliki multiple sessions
     * Relasi: mentoring_sessions.mentoring_group_id -> mentoring_groups.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mentoringGroup()
    {
        return $this->belongsTo(MentoringGroup::class, 'mentoring_group_id');
    }

    /**
     * Sesi punya banyak Attendance records
     * Attendance mencatat kehadiran setiap mentee di sesi ini
     * Relasi: mentoring_sessions.id -> attendances.session_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Sesi punya banyak ProgressReport records
     * Progress report adalah catatan mentor tentang perkembangan mentee per sesi
     * Relasi: mentoring_sessions.id -> progress_reports.session_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function progressReports()
    {
        return $this->hasMany(ProgressReport::class);
    }
}
