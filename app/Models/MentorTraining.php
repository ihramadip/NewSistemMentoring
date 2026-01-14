<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorTraining extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'description',
        'schedule_date',
        'schedule_time',
        'material_link',
        'test_link',
    ];

    protected $casts = [
        'schedule_date' => 'date',
    ];
}