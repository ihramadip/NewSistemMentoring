<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['session_id','mentee_id','status','notes'];

    public function session() {
        return $this->belongsTo(Session::class);
    }
    public function mentee() {
        return $this->belongsTo(User::class, 'mentee_id');
    }

}
