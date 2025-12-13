<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MentoringGroup extends Model
{
    protected $fillable = ['name','mentor_id','level_id','schedule_info'];

    public function mentor() {
        return $this->belongsTo(User::class, 'mentor_id');
    }
    public function level() {
        return $this->belongsTo(Level::class);
    }
    public function members() {
        return $this->belongsToMany(User::class, 'group_members', 'mentoring_group_id', 'mentee_id')
                    ->withTimestamps();
    }
    public function sessions() {
        return $this->hasMany(Session::class);
    }

}
