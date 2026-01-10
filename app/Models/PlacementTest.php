<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlacementTest extends Model
{
    protected $fillable = [
        'mentee_id',
        'audio_recording_path',
        'audio_reading_score',
        'theory_score',
        'final_level_id',
    ];

    public function mentee()
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }

    public function finalLevel()
    {
        return $this->belongsTo(Level::class, 'final_level_id');
    }
}
