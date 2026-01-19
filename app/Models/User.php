<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model
 *
 * Model utama yang merepresentasikan user dalam sistem.
 * User dapat memiliki 3 role: Admin, Mentor (Pementor), atau Mentee
 *
 * Attributes:
 * - name: Nama lengkap user
 * - npm: Nomor Pokok Mahasiswa (untuk identitas unik)
 * - email: Email user
 * - password: Password terenkripsi
 * - role_id: Foreign key ke Role (Admin, Mentor, Mentee)
 * - faculty_id: Foreign key ke Faculty (Fakultas/Jurusan)
 * - gender: Jenis kelamin (untuk pemisahan ikhwan/akhwat di grouping)
 * 
 * @property-read \App\Models\Role|null $role
 * @property-read \App\Models\Faculty|null $faculty
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Attributes yang bisa di-mass assign (fill)
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'npm',
        'email',
        'password',
        'role_id',
        'faculty_id',
        'gender',
    ];

    /**
     * Attributes yang disembunyikan saat serialisasi (JSON response)
     * Password & remember_token tidak boleh terlihat di API/response
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ========== RELATIONSHIPS: Role & Faculty ==========

    /**
     * User memiliki 1 Role
     * Role menentukan permission & access level (Admin, Mentor, Mentee)
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * User memiliki 1 Faculty
     * Faculty digunakan untuk grouping mentee (lihat AutoGroupingService)
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    // ========== RELATIONSHIPS: Mentor Role ==========

    /**
     * User sebagai Mentor memiliki banyak MentoringGroup
     * Satu mentor dapat mengelola 1 kelompok mentoring
     * Relasi: users.id -> mentoring_groups.mentor_id
     */
    public function mentoringGroupsAsMentor()
    {
        return $this->hasMany(MentoringGroup::class, 'mentor_id');
    }

    // ========== RELATIONSHIPS: Mentee Role ==========

    /**
     * User sebagai Mentee memiliki banyak MentoringGroup (many-to-many)
     * Satu mentee dapat join multiple groups (namun biasanya hanya 1)
     * Relasi: users.id -> group_members.mentee_id -> mentoring_groups.id
     * withTimestamps() untuk track kapan mentee bergabung
     */
    public function mentoringGroupsAsMentee()
    {
        return $this->belongsToMany(
            MentoringGroup::class,
            'group_members',
            'mentee_id',
            'mentoring_group_id'
        )->withTimestamps();
    }

    /**
     * User sebagai Mentee punya 1 GroupMember record
     * Menyimpan metadata keanggotaan di group (status, joined_at, etc)
     */
    public function groupMember()
    {
        return $this->hasOne(GroupMember::class, 'mentee_id');
    }

    /**
     * User sebagai Mentee punya 1 PlacementTest
     * Placement test dilakukan untuk menentukan level mentoring
     * Relasi: users.id -> placement_tests.mentee_id
     */
    public function placementTest()
    {
        return $this->hasOne(PlacementTest::class, 'mentee_id');
    }

    // ========== RELATIONSHIPS: Mentor Application ==========

    /**
     * User punya 1 MentorApplication (jika pendaftar sebagai calon mentor)
     * Menyimpan data CV, rekaman, status seleksi mentor
     */
    public function mentorApplication()
    {
        return $this->hasOne(MentorApplication::class);
    }

    // ========== RELATIONSHIPS: Attendance & Progress ==========

    /**
     * User sebagai Mentee punya banyak Attendance records
     * Attendance mencatat kehadiran di setiap session mentoring
     * Relasi: users.id -> attendances.mentee_id
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'mentee_id');
    }

    /**
     * User sebagai Mentee punya banyak ProgressReport
     * Progress report berisi catatan mentor per session mentoring
     * Relasi: users.id -> progress_reports.mentee_id
     */
    public function progressReports()
    {
        return $this->hasMany(ProgressReport::class, 'mentee_id');
    }

    // ========== RELATIONSHIPS: Announcements ==========

    /**
     * User sebagai Author punya banyak Announcement
     * Admin membuat pengumuman untuk dilihat mentee & mentor
     * Relasi: users.id -> announcements.author_id
     */
    public function announcementsAuthored()
    {
        return $this->hasMany(Announcement::class, 'author_id');
    }

    // ========== ATTRIBUTE CASTING ==========

    /**
     * Cast attributes ke tipe yang sesuai
     * email_verified_at: Convert ke DateTime object
     * password: Hash otomatis saat assign
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
