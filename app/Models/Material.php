<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['level_id','title','file_path','description'];

    public function level() {
        return $this->belongsTo(Level::class);
    }

}
