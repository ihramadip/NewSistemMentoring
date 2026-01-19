<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Level Model
 *
 * Model untuk mendefinisikan level/tingkat mentoring (MODULE B #4 & C #4)
 * Level menentukan konten, materi, dan pengelompokan mentee
 *
 * Level yang tersedia:
 * - Fasih: Level tertinggi (sudah mahir membaca Al-Qur'an)
 * - Ibtida: Level pemula (dasar membaca)
 * - Hijaiyah 1: Level sangat pemula (baru kenal huruf)
 * - Hijaiyah 2: Level sangat pemula (belum lancar huruf)
 *
 * Attributes:
 * - name: Nama level (Fasih, Ibtida, Hijaiyah 1, Hijaiyah 2)
 * - description: Deskripsi level & kriteria penempatan
 * - timestamps: created_at & updated_at otomatis
 *
 * @package App\Models
 */
class Level extends Model
{
    /**
     * Attributes yang bisa di-mass assign
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Level punya banyak Material
     * Setiap level memiliki materi pembelajaran yang spesifik
     * Relasi: levels.id -> materials.level_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    /**
     * Level punya banyak MentoringGroup
     * Setiap kelompok mentoring ditugaskan untuk satu level
     * Relasi: levels.id -> mentoring_groups.level_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mentoringGroups()
    {
        return $this->hasMany(MentoringGroup::class);
    }
}
