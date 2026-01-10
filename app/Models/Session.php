<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $table = 'mentoring_sessions'; // Explicitly set the table name
    
    Protected $fillable = ['mentoring_group_id','session_number','date','topic'];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function group() {
        return $this->belongsTo(MentoringGroup::class, 'mentoring_group_id');
    }
    public function attendances() {
        return $this->hasMany(Attendance::class);
    }
    public function progressReports() {
        return $this->hasMany(ProgressReport::class);
    }
    
}
