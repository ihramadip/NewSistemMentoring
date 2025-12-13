<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['author_id','title','content','target_role'];

    public function author() {
        return $this->belongsTo(User::class, 'author_id');
    }

}
