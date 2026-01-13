<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
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
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Roles & faculty
    public function role() {
        return $this->belongsTo(Role::class);
    }
    public function faculty() {
        return $this->belongsTo(Faculty::class);
    }

    // As mentor
    public function mentoringGroupsAsMentor() {
        return $this->hasMany(MentoringGroup::class, 'mentor_id');
    }

    // As mentee
    public function mentoringGroupsAsMentee() {
        return $this->belongsToMany(MentoringGroup::class, 'group_members', 'mentee_id', 'mentoring_group_id')
                    ->withTimestamps();
    }

    public function groupMember() {
        return $this->hasOne(GroupMember::class, 'mentee_id');
    }

    public function placementTest() {
        return $this->hasOne(PlacementTest::class, 'mentee_id');
    }

    // Applications
    public function mentorApplication() {
        return $this->hasOne(MentorApplication::class);
    }

    // Attendance & Progress
    public function attendances() {
        return $this->hasMany(Attendance::class, 'mentee_id');
    }
    public function progressReports() {
        return $this->hasMany(ProgressReport::class, 'mentee_id');
    }

    // Announcements
    public function announcementsAuthored() {
        return $this->hasMany(Announcement::class, 'author_id');
    }

    /**
     * Get the attributes that should be cast.
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
