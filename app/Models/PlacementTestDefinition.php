<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlacementTestDefinition extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get all of the questions for the placement test.
     */
    public function questions()
    {
        return $this->morphMany(Question::class, 'questionable');
    }
}