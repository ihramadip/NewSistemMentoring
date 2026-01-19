<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Role Model
 *
 * Model untuk mendefinisikan role/peran dalam sistem
 * Role digunakan untuk Role-Based Access Control (RBAC)
 *
 * Role yang tersedia:
 * - Admin: Mengelola sistem, mentor, mentee, ujian, laporan
 * - Mentor: Mengelola kelompok mentee, input laporan progres, nilai
 * - Mentee: Mengikuti sesi mentoring, ujian, lihat nilai & progres
 *
 * Attributes:
 * - name: Nama role (Admin, Mentor, Mentee)
 * - timestamps: created_at & updated_at otomatis
 *
 * @package App\Models
 */
class Role extends Model
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
     * Role punya banyak User
     * Satu role dapat dimiliki oleh multiple users
     * Relasi: roles.id -> users.role_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
