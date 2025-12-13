<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = ['name', 'description'];

    public function materials() {
        return $this->hasMany(Material::class);
    }

    public function mentoringGroups() {
        return $this->hasMany(MentoringGroup::class);
    }

}
