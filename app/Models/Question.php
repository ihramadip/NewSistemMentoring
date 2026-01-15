<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'questionable_id',
        'questionable_type',
        'question_text',
        'question_type',
        'score_value',
    ];

    public function questionable()
    {
        return $this->morphTo();
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }
}
