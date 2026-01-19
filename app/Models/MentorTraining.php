<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * MentorTraining Model
 *
 * Model untuk program pelatihan/training mentor (MODULE A #4: Training for Mentor & #5: Diklat)
 * Menyimpan jadwal dan materi training yang harus diikuti oleh calon/mentor
 *
 * Attributes:
 * - title: Judul program training (misal: "TFM - Basic Mentoring Skills")
 * - type: Tipe training (TFM = Training for Mentor, Diklat = Training continuation)
 * - description: Deskripsi/isi program training
 * - schedule_date: Tanggal pelaksanaan training
 * - schedule_time: Waktu pelaksanaan (jam berapa)
 * - material_link: Link ke materi pembelajaran (Google Drive, Dropbox, dll)
 * - test_link: Link ke pre-test/post-test (Google Form, Quiz, dll)
 * - timestamps: created_at & updated_at otomatis
 *
 * @package App\Models
 */
class MentorTraining extends Model
{
    use HasFactory;

    /**
     * Attributes yang bisa di-mass assign
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'type',
        'description',
        'schedule_date',
        'schedule_time',
        'material_link',
        'test_link',
    ];

    /**
     * Cast attributes ke tipe yang sesuai
     * schedule_date: Convert ke Date object
     *
     * @var array<string, string>
     */
    protected $casts = [
        'schedule_date' => 'date',
    ];
}