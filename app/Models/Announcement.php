<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Announcement Model
 *
 * Model untuk pengumuman sistem ke mentee & mentor (MODULE C #5 & MODULE D: Admin Dashboard)
 * Admin dapat membuat pengumuman untuk dikirim ke target audience tertentu
 *
 * Attributes:
 * - author_id: Foreign key ke User (admin pembuat pengumuman)
 * - title: Judul pengumuman
 * - content: Isi/konten pengumuman (text atau HTML)
 * - target_role: Penerima pengumuman (Admin, Mentor, Mentee, atau All)
 * - published_at: Tanggal publikasi (untuk scheduling pengumuman)
 * - timestamps: created_at & updated_at otomatis
 *
 * @package App\Models
 */
class Announcement extends Model
{
    use HasFactory;

    /**
     * Attributes yang bisa di-mass assign
     *
     * @var list<string>
     */
    protected $fillable = [
        'author_id',
        'title',
        'content',
        'target_role',
        'published_at',
    ];

    /**
     * Cast attributes ke tipe yang sesuai
     * published_at: Convert ke DateTime object untuk handling tanggal publikasi
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Pengumuman dibuat oleh 1 User (Admin)
     * Mencatat siapa pembuat pengumuman untuk audit trail
     * Relasi: announcements.author_id -> users.id
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
