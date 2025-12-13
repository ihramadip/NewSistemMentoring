<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MentorApplication extends Model
{
    protected $fillable = [
        'user_id','cv_path','recording_path','btaq_history','status','notes_from_reviewer'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

}
