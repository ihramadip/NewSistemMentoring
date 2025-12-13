<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    Protected $fillable = ['mentoring_group_id','session_number','date','topic'];

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
