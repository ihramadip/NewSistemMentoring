<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * PlacementTest Model
 *
 * Model untuk hasil tes penempatan mentee (MODULE B #3: Placement Test)
 * Tes penempatan terdiri dari 2 bagian: tes bacaan (audio) & tes tajwid (teori)
 * Hasil tes menentukan level mentoring: Fasih, Ibtida, Hijaiyah 1, Hijaiyah 2
 *
 * Attributes:
 * - mentee_id: Foreign key ke User (mentee yang mengerjakan tes)
 * - audio_recording_path: Path file rekaman bacaan mentee
 * - audio_reading_score: Nilai tes bacaan (skala 0-100)
 * - theory_score: Nilai tes tajwid/teori (skala 0-100)
 * - final_level_id: Foreign key ke Level (hasil placement test)
 * - timestamps: created_at & updated_at otomatis
 *
 * @package App\Models
 */
class PlacementTest extends Model
{
    /**
     * Attributes yang bisa di-mass assign
     *
     * @var list<string>
     */
    protected $fillable = [
        'mentee_id',
        'audio_recording_path',
        'audio_reading_score',
        'theory_score',
        'final_level_id',
    ];

    /**
     * Tes punya 1 Mentee (User dengan role=mentee)
     * Satu mentee hanya bisa ambil placement test 1x
     * Relasi: placement_tests.mentee_id -> users.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mentee()
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }

    /**
     * Tes hasil ditentukan levelnya (final_level)
     * Level adalah hasil dari scoring tes (Fasih, Ibtida, dll)
     * Relasi: placement_tests.final_level_id -> levels.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function finalLevel()
    {
        return $this->belongsTo(Level::class, 'final_level_id');
    }
}
