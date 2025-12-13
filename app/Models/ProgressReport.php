<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressReport extends Model
{
    protected $fillable = ['session_id','mentee_id','score','reading_notes','general_notes'];

    public function session() {
        return $this->belongsTo(Session::class);
    }
    public function mentee() {
        return $this->belongsTo(User::class, 'mentee_id');
    }

}
