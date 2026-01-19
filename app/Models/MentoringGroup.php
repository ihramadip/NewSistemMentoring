<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * MentoringGroup Model
 *
 * Model untuk kelompok mentoring (MODULE A #6 & B #7: Penugasan & Pengelompokan)
 * Satu kelompok terdiri dari 1 mentor dan banyak mentee (ideal ±14 orang)
 *
 * Attributes:
 * - name: Nama kelompok (misal: "Kelompok A - Fasih")
 * - mentor_id: Foreign key ke User (mentor/pementor yang mengelola)
 * - level_id: Foreign key ke Level (Fasih, Ibtida, Hijaiyah 1, Hijaiyah 2)
 * - schedule_info: Informasi jadwal mentoring (hari, jam, tempat)
 * - timestamps: created_at & updated_at otomatis
 *
 * @package App\Models
 */
class MentoringGroup extends Model
{
    /**
     * Attributes yang bisa di-mass assign
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'mentor_id',
        'level_id',
        'schedule_info',
    ];

    /**
     * Kelompok punya 1 Mentor (User dengan role=mentor)
     * Relasi: mentoring_groups.mentor_id -> users.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    /**
     * Kelompok punya 1 Level
     * Level menentukan konten mentoring (Fasih, Ibtida, dll)
     * Relasi: mentoring_groups.level_id -> levels.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * Kelompok punya banyak Member Mentee (many-to-many)
     * Relasi: mentoring_groups.id -> group_members.mentoring_group_id -> users.id
     * withTimestamps() mencatat kapan mentee join/leave grup
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(
            User::class,
            'group_members',
            'mentoring_group_id',
            'mentee_id'
        )->withTimestamps();
    }

    /**
     * Kelompok punya banyak Session
     * Session adalah pertemuan mentoring (7 wajib + 21 tambahan)
     * Relasi: mentoring_groups.id -> mentoring_sessions.mentoring_group_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}
