<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Faculty Model
 *
 * Model untuk mendefinisikan fakultas/jurusan mahasiswa (MODULE B #1 & B #7)
 * Faculty digunakan untuk grouping mentee pada proses pengelompokan otomatis
 * Mentee dari fakultas yang sama idealnya dikelompokkan bersama (kuota ~50 per kelas)
 *
 * Attributes:
 * - name: Nama fakultas/jurusan (Teknik, Ekonomi, Hukum, dll)
 * - timestamps: created_at & updated_at otomatis
 *
 * @package App\Models
 */
class Faculty extends Model
{
    /**
     * Attributes yang bisa di-mass assign
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Faculty punya banyak User
     * Setiap user (mentee/mentor) berasal dari satu fakultas
     * Relasi: faculties.id -> users.faculty_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
