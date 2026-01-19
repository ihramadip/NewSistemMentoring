<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Material Model
 *
 * Model untuk materi pembelajaran mentoring (MODULE C #4: Materi Belajar per Level)
 * Setiap level memiliki materi-materi yang disesuaikan dengan tingkat kesulitannya
 * Mentee dapat mengakses materi sesuai dengan level mereka
 *
 * Attributes:
 * - level_id: Foreign key ke Level (materi untuk level apa)
 * - title: Judul materi (misal: "Cara Baca Huruf Muqaddamah")
 * - file_path: Path file materi (PDF, video, atau dokumen lainnya)
 * - description: Deskripsi singkat tentang isi materi
 * - timestamps: created_at & updated_at otomatis
 *
 * @package App\Models
 */
class Material extends Model
{
    /**
     * Attributes yang bisa di-mass assign
     *
     * @var list<string>
     */
    protected $fillable = [
        'level_id',
        'title',
        'file_path',
        'description',
    ];

    /**
     * Material punya 1 Level
     * Materi adalah resource pembelajaran yang spesifik untuk satu level
     * Relasi: materials.level_id -> levels.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}
